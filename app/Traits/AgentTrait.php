<?php

namespace App\Traits;

use App\Models\UserDevice;
use Jenssegers\Agent\Agent;

trait AgentTrait
{
    /**
     * Agent reports platforms by their internal key ('AndroidOS', 'OS X').
     * Only the ones that read badly in the admin tables are relabelled;
     * anything else is stored as the package returns it.
     */
    protected static array $agent_platform_labels = [
        'AndroidOS' => 'Android',
        'OS X' => 'macOS',
        'Windows NT' => 'Windows',
        'WindowsPhoneOS' => 'Windows Phone',
        'WindowsMobileOS' => 'Windows Mobile',
        'BlackBerryOS' => 'BlackBerry',
    ];

    public function checkAgent(string $guard = 'web', $user_api = null): ?int
    {
        $user = auth($guard)->user() ?: $user_api;

        if (! $user) {
            return null;
        }

        return UserDevice::updateOrCreateDevice($this->agentAttributes($user->id));
    }

    protected function agentAttributes($user_id): array
    {
        $agent = $this->agent();
        $is_desktop = $agent->isDesktop();

        return [
            'user_id' => $user_id,
            'ip' => request()->ip() ?: '',
            'platform' => $this->agentPlatform($agent),
            'browser' => $this->agentValue($agent->browser()),
            'is_desktop' => $is_desktop,
            'is_phone' => $agent->isPhone(),
            'device' => $is_desktop ? null : $this->agentDevice($agent),
        ];
    }

    protected function agent(): Agent
    {
        $agent = new Agent;
        $agent->setUserAgent((string) request()->userAgent());

        return $agent;
    }

    protected function agentPlatform(Agent $agent): ?string
    {
        $platform = $this->agentValue($agent->platform());

        return $platform ? (static::$agent_platform_labels[$platform] ?? $platform) : null;
    }

    /**
     * Agent::device() falls back to its "utility" rules when no real device
     * matches, so an unrecognised Android phone comes back as 'WebKit' and a
     * crawler as 'Bot'. Those are not model names: drop them and read the
     * model out of the user agent instead.
     */
    protected function agentDevice(Agent $agent): ?string
    {
        $device = $this->agentValue($agent->device());

        if ($device !== null && ! array_key_exists($device, Agent::getUtilities())) {
            return $device;
        }

        if (preg_match('/Android[^;]*;\s*([^;)]+?)(?:\s+Build|[;)])/i', (string) request()->userAgent(), $matches)) {
            return trim($matches[1]) ?: null;
        }

        return null;
    }

    /** Agent returns false, not null, for anything it cannot identify. */
    protected function agentValue($value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
