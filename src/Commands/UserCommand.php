<?php

namespace Poppy\System\Commands;

use Illuminate\Console\Command;
use Poppy\System\Action\Pam;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;
use Poppy\System\Models\SysConfig;
use Throwable;

/**
 * User
 */
class UserCommand extends Command
{
    /**
     * 前端部署.
     * @var string
     */
    protected $signature = 'py-system:user 
		{do : actions in "reset_pwd"}
		{--account= : Account Name}
		{--pwd= : Account password}
		';

    /**
     * 描述
     * @var string
     */
    protected $description = 'user handler.';

    /**
     * Execute the console command.
     * @return mixed
     * @throws Throwable
     */
    public function handle()
    {
        $do = $this->argument('do');
        switch ($do) {
            case 'reset_pwd':
                $passport = $this->ask('Your passport?');

                if ($pam = PamAccount::passport($passport)) {
                    $pwd = trim($this->ask('Your aim password'));
                    $Pam = new Pam();
                    $Pam->setPassword($pam, $pwd);
                    $this->info('Reset user password success');
                }
                else {
                    $this->error('Your account not exists');
                }
                break;
            case 'create_user':
                $passport = $this->ask('Please input passport!');
                $password = $this->ask('Please input password!');
                $role     = $this->ask('Please input role name!');
                if (!$pam = PamAccount::passport($passport)) {
                    $Pam = new Pam();
                    if ($Pam->register($passport, $password, $role)) {
                        $this->info('User ' . $passport . ' created');
                    }
                    else {
                        $this->error($Pam->getError());
                    }
                }
                else {
                    $this->error('user ' . $passport . ' exists');
                }
                break;
            case 'init_role':
                $roles = [
                    [
                        'name'      => PamRole::FE_USER,
                        'title'     => '用户',
                        'type'      => PamAccount::TYPE_USER,
                        'is_system' => SysConfig::YES,
                    ],
                    [
                        'name'      => PamRole::BE_ROOT,
                        'title'     => '超级管理员',
                        'type'      => PamAccount::TYPE_BACKEND,
                        'is_system' => SysConfig::YES,
                    ],
                    [
                        'name'      => PamRole::DEV_USER,
                        'title'     => '开发者',
                        'type'      => PamAccount::TYPE_DEVELOP,
                        'is_system' => SysConfig::YES,
                    ],
                ];
                foreach ($roles as $role) {
                    if (!PamRole::where('name', $role['name'])->exists()) {
                        PamRole::create($role);
                    }
                }
                $this->info('Init Role success');
                break;
            case 'auto_enable':
                (new Pam())->autoEnable();
                $this->info(sys_mark('py-system', __CLASS__, 'auto enable pam!'));
                break;
            case 'clear_log':
                (new Pam())->clearLog();
                $this->info(sys_mark('py-system', __CLASS__, 'auto clear log!'));
                break;
            default:
                $this->error('Please type right action![reset_pwd, init_role, create_user]');
                break;
        }
    }
}