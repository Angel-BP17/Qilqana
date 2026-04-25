<?php

namespace App\Observers;

use App\Jobs\LogActivityJob;
use App\Models\Charge;
use App\Models\Signature;
use Illuminate\Database\Eloquent\Model;

class ActivityObserver
{
    public function created(Model $model): void
    {
        if ($model instanceof Signature) {
            return;
        }

        $this->dispatchLog('create', $model, null, $this->cleanAttributes($model));
    }

    public function updated(Model $model): void
    {
        if ($model instanceof Signature) {
            $this->handleSignatureUpdate($model);

            return;
        }

        $this->dispatchLog('update', $model, $this->cleanAttributes($model, true), $this->cleanAttributes($model));
    }

    public function deleted(Model $model): void
    {
        if ($model instanceof Signature) {
            return;
        }

        $this->dispatchLog('delete', $model, $this->cleanAttributes($model), null);
    }

    protected function handleSignatureUpdate(Signature $signature): void
    {
        $originalStatus = $signature->getOriginal('signature_status');
        $currentStatus = $signature->signature_status;

        if ($originalStatus === $currentStatus) {
            return;
        }

        if (! in_array($currentStatus, ['firmado', 'rechazado'], true)) {
            return;
        }

        $action = $currentStatus === 'firmado' ? 'sign' : 'reject';
        $charge = $signature->charge;

        $before = ['signature_status' => $originalStatus];
        $after = ['signature_status' => $currentStatus];
        if ($charge instanceof Charge) {
            $before['charge_id'] = $charge->id;
            $after['charge_id'] = $charge->id;
        }

        $reason = request()?->input('reason');
        if (! $reason && $currentStatus === 'rechazado') {
            $reason = request()?->input('signature_comment');
        }

        $userId = auth()?->id() ?? $signature->signed_by;

        LogActivityJob::dispatch([
            'user_id' => $userId,
            'action' => $action,
            'model' => 'Charge',
            'before' => $before,
            'after' => $after,
            'reason' => $reason,
        ]);
    }

    protected function dispatchLog(string $action, Model $model, ?array $before, ?array $after): void
    {
        LogActivityJob::dispatch([
            'user_id' => auth()?->id(),
            'action' => $action,
            'model' => class_basename($model),
            'before' => $before,
            'after' => $after,
            'reason' => request()?->input('reason'),
        ]);
    }

    protected function cleanAttributes(Model $model, bool $original = false): array
    {
        $attributes = $original ? $model->getOriginal() : $model->getAttributes();
        $hidden = method_exists($model, 'getHidden') ? $model->getHidden() : [];
        $hidden[] = 'password';

        return array_diff_key($attributes, array_flip($hidden));
    }
}
