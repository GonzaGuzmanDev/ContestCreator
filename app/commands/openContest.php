<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Services\OxoMailer;

class openContest extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'contest:open';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $contests = Contest::all();
        $time = date("Y-m-d H:i:s");
        $superAdmins = User::where('super', 1)->select('email')->get();
        foreach($contests as $contest){
            if(isset($contest->start_at) && $contest->start_at <= $time && $contest->status == Contest::STATUS_READY){
                $contest->status = Contest::STATUS_PUBLIC;
                $contest->save();
                foreach($superAdmins as $super){
                    OxoMailer::sendMail([
                        'email_to' => (string)$super->email,
                        'subject' => 'Festival Publico: '.$contest->name,
                        'body' => 'El siguiente Festival: "'.$contest->name.'" esta en estado Publico. <br> Link: https://www.oxoawards.com/'.$contest->code.'/',
                    ]);
                }
            }

            if(isset($contest->finish_at) && $contest->finish_at < $time && $contest->status == Contest::STATUS_PUBLIC){
                $contest->status = Contest::STATUS_CLOSED;
                $contest->save();
                foreach($superAdmins as $super){
                    OxoMailer::sendMail([
                        'email_to' => $super->email,
                        'subject' => 'Festival Cerrado: '.$contest->name,
                        'body' => 'El siguiente Festival: "'.$contest->name.'" ha sido cerrado. <br> Link: https://www.oxoawards.com/'.$contest->code.'/',
                    ]);
                }
            }
        }
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
