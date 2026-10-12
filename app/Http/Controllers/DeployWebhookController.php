<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class DeployWebhookController extends Controller
{
    /**
     * GitHub calls this on every push. We verify the request really came
     * from GitHub (via the shared secret), then kick off deploy.sh in the
     * background so this request can return immediately — GitHub marks the
     * delivery as failed if it doesn't get a response within ~10 seconds.
     */
    public function handle(Request $request): Response
    {
        $secret = config('services.deploy_webhook.secret');

        if (! $secret) {
            Log::warning('Deploy webhook hit but DEPLOY_WEBHOOK_SECRET is not set.');

            return response('Webhook secret not configured on server.', 500);
        }

        $signature = $request->header('X-Hub-Signature-256', '');
        $expected = 'sha256='.hash_hmac('sha256', $request->getContent(), $secret);

        if (! hash_equals($expected, $signature)) {
            Log::warning('Deploy webhook received an invalid signature.');

            return response('Invalid signature.', 401);
        }

        if ($request->header('X-GitHub-Event') !== 'push') {
            return response('Ignored (not a push event).', 200);
        }

        if (($request->input('ref')) !== 'refs/heads/main') {
            return response('Ignored (not the main branch).', 200);
        }

        try {
            Process::path(base_path())->timeout(0)->start('nohup bash deploy.sh >> storage/logs/deploy.log 2>&1 &');
        } catch (\Throwable $e) {
            Log::error('Failed to start deploy.sh: '.$e->getMessage());

            return response('Could not start deploy script — shell exec may be disabled on this host.', 500);
        }

        return response('Deploy started.', 200);
    }
}
