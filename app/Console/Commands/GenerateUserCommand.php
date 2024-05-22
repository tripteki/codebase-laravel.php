<?php

namespace App\Console\Commands;

use Tripteki\Helpers\Helpers\ProjectHelper;
use Tripteki\User\Contracts\Repository\Admin\IUserRepository as IUserAdminRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateUserCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = "adminer:generate:user {--S|superuser : Generate superuser}";

    /**
     * @var string
     */
    protected $description = "Generate user";

    /**
     * @var \Tripteki\Helpers\Helpers\ProjectHelper
     */
    protected $helper;

    /**
     * @var \Tripteki\User\Contracts\Repository\Admin\IUserRepository
     */
    protected $userAdminRepository;

    /**
     * @param \Tripteki\Helpers\Helpers\ProjectHelper $helper
     * @param \Tripteki\User\Contracts\Repository\Admin\IUserRepository $userAdminRepository
     * @return void
     */
    public function __construct(ProjectHelper $helper, IUserAdminRepository $userAdminRepository)
    {
        parent::__construct();

        $this->helper = $helper;
        $this->userAdminRepository = $userAdminRepository;
    }

    /**
     * @return int
     */
    public function handle()
    {
        if (! $this->option("superuser")) {

            $name = $this->ask("What is the user name?");
            $email = $this->ask("What is the user email?");
            $password = $this->secret("What is the user password?");

            $this->generateUser($name, $email, $password);

        } else {

            if (class_exists("Tripteki\ACL\Providers\ACLServiceProvider")) {

                $name = \Tripteki\ACL\Providers\ACLServiceProvider::$SUPERUSER;
                $email = $name."@mail.com";
                $password = Str::random(8);

                $user = $this->generateUser($name, $email, $password);

                $superadmin = \Tripteki\ACL\Providers\ACLServiceProvider::$SUPERADMIN;

                $repository = app(\Tripteki\ACL\Contracts\Repository\IACLRepository::class);
                $repository->setUser($user);
                $repository->grantAs($superadmin);
            }
        }

        return 0;
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $password
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function generateUser($name, $email, $password)
    {
        $model = $this->userAdminRepository->create(
        $user = [

            "name" => $name,
            "email" => $email,
            "password" => $password,
        ]);

        if (! $model) {

            $this->error("The user existed.");

            exit();
        }

        $this->line('<comment>Here is your new user. This is the only time it will be shown the password so don\'t lose it!</comment>');

        $this->table(
            collect($user)->map(function ($item, $field) { return Str::ucfirst($field); })->toArray(),
            [ array_values($user), ],
        );

        return $model;
    }
};
