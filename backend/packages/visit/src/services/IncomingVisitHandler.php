<?php

namespace EventLab\Visit\Services;

use EventLab\Core\Services\HandleFactory;
use EventLab\Core\Services\SimpleAgent;
use EventLab\Database\Services\LutHandler;

class IncomingVisitHandler
{
    private LutHandler $lutHandler;

    public function __construct($globalPdo)
    {
        $this->lutHandler = new LutHandler($globalPdo, new HandleFactory());
    }

    public function handleIncoming(object $args): string
    {
        $init = $args->init ?? null;
        if (!$init) {
            return json_encode(['status' => 'error', 'message' => 'Internal Server Error']);
        }

        $json    = base64_decode($init);
        $payload = json_decode($json);

        $simple  = new SimpleAgent();

        // Pass parsed info or user-agent string into injected service handlers
        $agent          = $simple->getSimpleInfo();
        $lutAgentHandle = $this->lutHandler->handle('lut_agents', $agent);

        // and all the other stuff that needs to be done ...

        return $lutAgentHandle;
    }
}
