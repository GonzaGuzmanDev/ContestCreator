<?php

class AdminController extends BaseController {

    public function getMetrics(){
        $metrics = new SystemMetrics();
        return Response::json(['status' => 200, 'metrics' => $metrics->metrics]);
    }
    public function executeGCManager(){
        ContestFile::executeEncoder();
        return Response::json(['status' => 200]);
    }
    public function assignQueuedToManualEncoder(){
        ContestFileVersion::where('cloud_encoder_id', '=', 'preemp-awards-encoder-global-1')
            ->whereIn('status', [0, 1])->update(['cloud_encoder_id' => 'awards-encoder-manual-1', 'status' => ContestFileVersion::QUEUED]);
        return Response::json(['status' => 200]);
    }
}