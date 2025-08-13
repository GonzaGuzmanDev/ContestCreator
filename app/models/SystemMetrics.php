<?php

use Carbon\Carbon;

class SystemMetrics {

    /** @var int
     * Tiempo que consideramos como conectado al usuario en minutos
     */
    const ONLINE_TIME_LIMIT = 2;
    /** @var string */
    protected $path;
    /** @var string */
    protected $iotop;
    /** @var string */
    protected $ifstat;
    /** @var array */
    public $metrics = [
        'diskUsage' => [],
        'cpuMemory' => [],
        'network' => [],
        'files' => [],
        'users' => [],
        'events' => [],
        'gcInstances' => []
    ];

    public function __construct() {
        $this->path = Config::get('metrics.root');
        $this->ifstat = Config::get('metrics.ifstat');
        $this->iotop = Config::get('metrics.iotop');
        $this->diskUsage();
        $this->cpuMemory();
        $this->network();
        $this->getUsers();
        $this->getContestsEvents();
        $this->getFiles();
        $this->getGCInstances();
    }

    protected function diskUsage() {
        return;
        $diskUsage = [];
        if (!file_exists($this->path)) {
            $diskUsage['error'] = "path_not_found";
            return;
        }
        $diskUsage['totalSpace'] = round(disk_total_space($this->path) / pow(1024, 4), 2);
        $diskUsage['freeSpace'] = round(disk_free_space($this->path) / pow(1024, 4), 2);
        $diskUsage['usedSpace'] = $diskUsage['totalSpace'] - $diskUsage['freeSpace'];
        if($diskUsage['totalSpace'] > 0)
            $diskUsage['percentageUsed'] = sprintf('%.2f',($diskUsage['usedSpace'] / $diskUsage['totalSpace']) * 100);
        $diskIO = shell_exec('cat '.$this->iotop);
        $diskIO = (string)trim($diskIO);
        $diskIOArr = explode("\n", $diskIO);
        foreach ($diskIOArr as $diskIOLine) {
            $output = [];
            preg_match("/Total DISK READ.*([0-9]{1,}\.[0-9]{1,})\s*(.*)\s*\|\s*Total DISK WRITE.*([0-9]{1,}\.[0-9]{1,})\s*(.*)/", $diskIOLine, $output);
            if($output){
                $diskUsage['totalDiskRead'] = $output[1]." ".trim($output[2]);
                $diskUsage['totalDiskWrite'] = $output[3]." ".trim($output[4]);
            }
            preg_match("/Actual DISK READ.*([0-9]{1,}\.[0-9]{1,})\s*(.*)\s*\|\s*Actual DISK WRITE.*([0-9]{1,}\.[0-9]{1,})\s*(.*)/", $diskIOLine, $output);
            if($output){
                $diskUsage['actualDiskRead'] = $output[1]." ".trim($output[2]);
                $diskUsage['actualDiskWrite'] = $output[3]." ".trim($output[4]);
            }
        }
        $this->metrics['diskUsage'] = $diskUsage;
    }

    protected function cpuMemory() {
        return;
        $cpuMemory = [];
        $cpuMemory['load'] = sys_getloadavg();
        $top = shell_exec('top -b -n1');
        $top = (string)trim($top);
        $topArr = explode("\n", $top);
        foreach ($topArr as $topLine) {
            $output = [];
            preg_match("/top - \d*:\d*:\d*\s*up\s*(\d*)\s/", $topLine, $output);
            if($output){
                $cpuMemory['upDays'] = $output[1];
            }
            $output = [];
            preg_match("/Cpu\(s\):\s*([0-9]{1,}\.[0-9]{1,})\s*us,\s*([0-9]{1,}\.[0-9]{1,})\s*sy,\s*([0-9]{1,}\.[0-9]{1,})\s*ni,\s*([0-9]{1,}\.[0-9]{1,})\s*id/", $topLine, $output);
            if($output){
                $cpuMemory['cpuUser'] = $output[1];
                $cpuMemory['cpuSystem'] = $output[2];
                $cpuMemory['cpuNice'] = $output[3];
                $cpuMemory['cpuIdle'] = $output[4];
            }
        }
        $memory = shell_exec('cat /proc/meminfo');
        $memory = (string)trim($memory);
        $memoryArr = explode("\n", $memory);
        foreach ($memoryArr as $memoryLine) {
            $output = [];
            preg_match("/(.*):\s*(.*)\s(.*)/", $memoryLine, $output);
            if($output[1] == 'MemTotal') $cpuMemory['memoryTotal'] = $output[2];
            if($output[1] == 'MemFree') $cpuMemory['memoryFree'] = $output[2];
            if($output[1] == 'MemAvailable') $cpuMemory['memoryAvailable'] = $output[2];
            if($output[1] == 'SwapTotal') $cpuMemory['memorySwapTotal'] = $output[2];
            if($output[1] == 'SwapFree') $cpuMemory['memorySwapFree'] = $output[2];
        }
        $this->metrics['cpuMemory'] = $cpuMemory;
    }

    protected function network(){
        return;
        $ifStat = shell_exec('tail -n 50 '.$this->ifstat);
        $ifStat = (string)trim($ifStat);
        $ifStatArr = explode("\n", $ifStat);
        $count = 0;
        $sumIn = 0;
        $sumOut = 0;
        $avgIn = 0;
        $avgOut = 0;
        $network = [];
        foreach ($ifStatArr as $ifStatLine) {
            $output = [];
            preg_match("/([0-9]{1,}:[0-9]{1,}:[0-9]{1,})\s*([0-9]{1,}\.[0-9]{1,})\s*([0-9]{1,}\.[0-9]{1,})/", $ifStatLine, $output);
            if ($output) {
                $network[] = array_slice($output, 1);
                $count++;
                $sumIn += $output[2];
                $sumOut += $output[3];
            }
        }
        if($count > 0){
            $avgIn = round($sumIn / $count, 2);
            $avgOut = round($sumOut / $count, 2);
        }
        $this->metrics['network'] = ['samples' => $count, 'avgIn' => $avgIn, 'avgOut' => $avgOut, 'unit' => 'KB/s', 'network' => $network];
    }

    protected function getUsers(){
        $totalUsers = User::count();
        $totalActiveUsers = User::where('active', 1)->count();
        $totalVerifiedUsers = User::where('verified', 1)->count();
        $usersConnected = User::where('last_seen_at', '>=', date('Y-m-d H:i:s', strtotime('-'.SystemMetrics::ONLINE_TIME_LIMIT.' minutes')))->get();
        $this->metrics['users'] = ['total' => $totalUsers, 'active' => $totalActiveUsers, 'verified' => $totalVerifiedUsers, 'connected' => $usersConnected];
    }

    protected function getContestsEvents(){
        $contests = Contest::all();
        $inscriptions = [
            "events" => [],
            "color" => '#428bca',
            "textColor" => 'white',
        ];
        $voting = [
            "events" => [],
            "color" => '#fdb913',
            "textColor" => 'black',
        ];
        $votingSessions = [
            "events" => [],
            "color" => '#ff3e3e',
            "textColor" => 'white',
        ];
        foreach ($contests as $contest) {
            $ciev = $contest->getInscriptionsEvents();
            if($ciev) $inscriptions['events'] = array_merge($inscriptions['events'], $ciev);
            $cvev = $contest->getVotingEvents();
            if($cvev) $voting['events'] = array_merge($voting['events'], $cvev);
            $cvsev = $contest->getVotingSessionsEvents();
            if($cvsev) $votingSessions['events'] = array_merge($votingSessions['events'], $cvsev);
        }
        array_push($this->metrics['events'], $inscriptions);
        array_push($this->metrics['events'], $voting);
        array_push($this->metrics['events'], $votingSessions);
    }

    protected function getFiles(){
        //Prune interrupted uploads
        $uploadsExpired = ContestFileVersion::where('status', '=', ContestFileVersion::UPLOADING)
            ->where('source', 1)
            ->where('uploaded_at', '<',Carbon::now()->subSeconds(ContestFileVersion::UPLOAD_INTERRUPTED_TIMEOUT))
            ->has('contestFile')
            //->get();
            ->update(['status'=>ContestFileVersion::UPLOAD_INTERRUPTED]);
        Log::info($uploadsExpired);

        $encodingCount = ContestFileVersion::where('status', ContestFileVersion::ENCODING)
            ->where('source', 0)
            ->whereHas('ContestFile', function($query){
                $query->whereHas('ContestFileVersions', function($query){
                    $query->where('source', 1)->where('status','=',ContestFileVersion::AVAILABLE);
                });
            })
            ->count();
        $queuedCount = ContestFileVersion::where('status', ContestFileVersion::QUEUED)
            ->where('source', 0)
            ->whereHas('ContestFile', function($query){
                $query->whereHas('ContestFileVersions', function($query){
                    $query->where('source', 1)->where('status','=',ContestFileVersion::AVAILABLE);
                });
            })
            ->count();
        $uploadingCount = ContestFileVersion::where('status', ContestFileVersion::UPLOADING)
            ->has('contestFile')->count();
        $list = ContestFileVersion::whereIn('status', [ContestFileVersion::ENCODING,ContestFileVersion::QUEUED])
            ->where('source', 0)
            ->with(['contestFile' => function($query){
                $query->with(['contest'=>function($query){
                    $query->select(['id','name','code']);
                },'user'])->select(['code','contest_id','id','user_id','name','type']);
            },'format'=>function($query){
                $query->select(['id','label','extension']);
            }])
            ->whereHas('ContestFile', function($query){
                $query->whereHas('ContestFileVersions', function($query){
                    $query->where('source', 1)->where('status','=',ContestFileVersion::AVAILABLE);
                });
            })
            ->orderBy('status', 'desc')
            ->orderBy('id', 'asc')
            ->select(['id','contest_file_id','format_id','status','percentage','updated_at','uploaded_at'])
            ->take(20)->get();
        $uploads = ContestFileVersion::where('status', '=', ContestFileVersion::UPLOADING)
            ->where('source', 1)
            ->with(['contestFile' => function($query){
                $query->with(['contest'=>function($query){
                    $query->select(['id','name','code']);
                },'user'])->select(['code','contest_id','id','user_id','name','type']);
            }])
            ->has('contestFile')
            ->orderBy('id', 'asc')
            //->select(['id','contest_file_id','status','percentage','updated_at','created_at','uploaded_at'])
            ->take(20)->get();
        $uploadsData = $uploads->toArray();
        $this->metrics['files'] = ['encoding' => $encodingCount, 'queued' => $queuedCount, 'uploading' => $uploadingCount, 'list' => $list, 'uploads' => $uploadsData];
    }

    protected function getGCInstances(){
        return;
        $instancesList = Cloud::Instance()->GetInstances();
        $currentList = [];
        foreach ($instancesList as $zone => $instances) {
            $currentList[$zone]['count'] = count($instances);
            /* @var $instance Google_Service_Compute_Instance */
            foreach($instances as $instance) {
                $currentList[$zone]['instances'][] = [
                    'name' => $instance->getName(),
                    'status' => $instance->getStatus(),
                    'created_at' => $instance->getCreationTimestamp()
                ];
            }
        }
        $this->metrics['gcInstances'] = $currentList;
    }
}