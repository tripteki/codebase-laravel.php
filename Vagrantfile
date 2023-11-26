Vagrant.configure("2") do |config|

    config.vm.define "codebase" do |codebase|

        codebase.vm.provider "virtualbox"
        codebase.vm.guest = :linux
        codebase.vm.box = "generic/alpine38"

        codebase.vm.synced_folder ".", "/vagrant"

        codebase.vm.network "forwarded_port", guest: 8000, host: 8000

        codebase.vm.provision "shell", run: "once", privileged: true, inline: "apk update"
        codebase.vm.provision "shell", run: "once", privileged: true, reboot: true, inline: <<-SCRIPT

            apk add php nodejs
            apk add composer npm 2> /dev/null

            apk add php-{ctype,fileinfo,mbstring,zip,gd,curl,bcmath,pcntl,sockets,session,tokenizer} php-{json,xml} php-{pdo,pdo_mysql,pdo_sqlite,mysqlnd,mysqli}
            printf "yes\n" | apk add php-xdebug
            printf "yes\n" | apk add php-swoole
            printf "yes\n" | apk add php-amqp
            printf "yes\n" | apk add php-rdkafka
            printf "no\n" | apk add php-redis

        SCRIPT
        codebase.vm.provision "shell", run: "always", privileged: false, inline: "cd /vagrant && APP_ENV=production ./bin/project -b -l -t -e -c"
        codebase.vm.provision "shell", run: "once", privileged: true, inline: "cd /vagrant && php artisan supervisor:start background"

    end

end
