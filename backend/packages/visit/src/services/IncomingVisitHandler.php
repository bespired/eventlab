<?php

namespace EventLab\Visit\Services;

use EventLab\Database\Services\LutAgent;
use EventLab\Database\Services\LutDevice; // Assuming LutDevice follows the same pattern

class IncomingVisitHandler
{
    private LutAgent $lutAgent;
    private LutDevice $lutDevice;

    public function __construct(LutAgent $lutAgent, LutDevice $lutDevice)
    {
        $this->lutAgent  = $lutAgent;
        $this->lutDevice = $lutDevice;
    }

    private function handleIncoming(object $args): string
    {
        $init = $args->init ?? null;
        if (!$init) {
            return json_encode(['status' => 'error', 'message' => 'Internal Server Error']);
        }

        $json    = base64_decode($init);
        $payload = json_decode($json);

        $simple  = new SimpleAgent();
        $browser = $simple->getSimpleInfo();

        // Pass parsed info or user-agent string into injected service handlers
        $lutAgentHandle  = $this->lutAgent->handle($browser);
        $lutDeviceHandle = $this->lutDevice->handle($simple->agentDevice());

        return json_encode([
            'browser'    => $browser,
            'args'       => $payload,
            'status'     => 'success',
            'message'    => 'handling the visit.',
            'handle'     => $lutAgentHandle,
            'device_lut' => $lutDeviceHandle,
        ], JSON_PRETTY_PRINT);
    }
}
