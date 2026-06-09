/**
 * Configuration.
 */

const fs = require('fs');
const path = require('path');

const backendPath = process.env.CODEBASE_BACKEND_PATH || __dirname;
const phpBinary = process.env.PHP_BINARY || 'php';
const logDir = path.join(backendPath, 'storage/logs/pm2');
const envPath = path.join(backendPath, '.env');
const fileEnv = fs.existsSync(envPath)
  ? fs
      .readFileSync(envPath, 'utf8')
      .split(/\r?\n/)
      .reduce((env, line) => {
        const normalized = line.trim();

        if (!normalized || normalized.startsWith('#')) {
          return env;
        }

        const separatorIndex = normalized.indexOf('=');

        if (separatorIndex === -1) {
          return env;
        }

        const key = normalized.slice(0, separatorIndex).trim();
        const value = normalized
          .slice(separatorIndex + 1)
          .trim()
          .replace(/^['"]|['"]$/g, '');

        env[key] = value;

        return env;
      }, {})
  : {};

const env = (key, fallback) => process.env[key] || fileEnv[key] || fallback;

const octaneHost = env('OCTANE_HOST', '127.0.0.1');
const octanePort = env('OCTANE_PORT', '8000');
const octaneWorkers = env('OCTANE_WORKERS', 'auto');
const octaneMaxRequests = env('OCTANE_MAX_REQUESTS', '500');
const queueNames = env('PM2_QUEUE_NAMES', 'default');
const queueSleep = env('PM2_QUEUE_SLEEP', '3');
const queueTries = env('PM2_QUEUE_TRIES', '3');
const queueMaxTime = env('PM2_QUEUE_MAX_TIME', '3600');
const reverbHost = env('REVERB_SERVER_HOST', '0.0.0.0');
const reverbPort = env('REVERB_SERVER_PORT', '8080');
const reverbPath = env('REVERB_SERVER_PATH', '');

const artisan = (args) => ({
  script: 'artisan',
  interpreter: phpBinary,
  args,
  cwd: backendPath,
});

module.exports = {
  apps: [
    {
      name: 'codebase-octane',
      ...artisan(
        [
          'octane:start',
          `--host=${octaneHost}`,
          `--port=${octanePort}`,
          `--workers=${octaneWorkers}`,
          `--max-requests=${octaneMaxRequests}`,
        ].join(' '),
      ),
      instances: 1,
      exec_mode: 'fork',
      autorestart: true,
      max_restarts: 10,
      min_uptime: '10s',
      restart_delay: 5000,
      kill_timeout: 10000,
      max_memory_restart: '512M',
      merge_logs: true,
      time: true,
      out_file: path.join(logDir, 'octane-out.log'),
      error_file: path.join(logDir, 'octane-error.log'),
    },

    {
      name: 'codebase-queue',
      ...artisan(
        [
          'queue:work',
          `--queue=${queueNames}`,
          `--sleep=${queueSleep}`,
          `--tries=${queueTries}`,
          `--max-time=${queueMaxTime}`,
        ].join(' '),
      ),
      instances: 1,
      exec_mode: 'fork',
      autorestart: true,
      max_restarts: 10,
      min_uptime: '10s',
      restart_delay: 5000,
      kill_timeout: 30000,
      max_memory_restart: '256M',
      merge_logs: true,
      time: true,
      out_file: path.join(logDir, 'queue-out.log'),
      error_file: path.join(logDir, 'queue-error.log'),
    },

    {
      name: 'codebase-scheduler',
      ...artisan('schedule:work'),
      instances: 1,
      exec_mode: 'fork',
      autorestart: true,
      max_restarts: 10,
      min_uptime: '10s',
      restart_delay: 5000,
      kill_timeout: 10000,
      max_memory_restart: '128M',
      merge_logs: true,
      time: true,
      out_file: path.join(logDir, 'scheduler-out.log'),
      error_file: path.join(logDir, 'scheduler-error.log'),
    },

    {
      name: 'codebase-reverb',
      ...artisan(
        [
          'reverb:start',
          `--host=${reverbHost}`,
          `--port=${reverbPort}`,
          ...(reverbPath ? [`--path=${reverbPath}`] : []),
        ].join(' '),
      ),
      instances: 1,
      exec_mode: 'fork',
      autorestart: true,
      autostart: false,
      max_restarts: 10,
      min_uptime: '10s',
      restart_delay: 5000,
      kill_timeout: 10000,
      max_memory_restart: '256M',
      merge_logs: true,
      time: true,
      out_file: path.join(logDir, 'reverb-out.log'),
      error_file: path.join(logDir, 'reverb-error.log'),
    },
  ],
};
