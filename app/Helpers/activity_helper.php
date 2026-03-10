<?php

use App\Models\ActivityLog;

if (!function_exists('activity_log')) {

    function activity_log($action, $description = null, $model = null)
    {
        ActivityLog::create([
            // 'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description
        ]);
    }

}
