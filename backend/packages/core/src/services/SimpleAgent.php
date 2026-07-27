<?php
namespace EventLab\Core\Services;

class SimpleAgent
{
    public function getSimpleInfo(?string $ua = null): string
    {
        if ($ua === null) {
            $ua = $_SERVER['HTTP_USER_AGENT'];
        }

        if (strpos($ua, 'bot/') > 0) {
            return $this->operatingSystem($ua);
        }

        return sprintf("%s;%s;%s",
            $this->agentBrowser($ua),
            $this->operatingSystem($ua),
            $this->agentDevice($ua),
        );
    }

    public function operatingSystem(string $ua)
    {
        $lower = strtolower($ua);
        $parts = explode(' ', $ua);

        $checks = [
            'windows'    => 'Windows',
            'windows nt' => 'Windows NT',
            'linux'      => 'Linux',
            'macintosh'  => 'Mac OS X',
            'os x'       => 'Mac OS X',
            'cros'       => 'ChromeOS',
            'android'    => 'Android',
            'iphone'     => 'iOS',
            'ipad'       => 'iOS',
            'ipod'       => 'iOS',
        ];

        $os = "";
        foreach ($checks as $key => $value) {
            $os = strpos($lower, $key) > 0 ? $value : $os;
        }

        if (strpos($lower, 'bot/') > 0) {
            foreach ($parts as $part) {
                if (strpos($part, 'bot/') > 0) {
                    $os = $part;
                }
            }
        }

        return $os;
    }

    public function agentDevice(string $ua)
    {
        $lower = strtolower($ua);

        $checks = [
            'tablet'     => 'Tablet',
            'ipad'       => 'Tablet',
            'playbook'   => 'Tablet',
            'silk'       => 'Tablet',

            'mobile'     => 'Mobile',
            'ipod'       => 'Mobile',
            'iphone'     => 'Mobile',
            'android'    => 'Mobile',
            'blackberry' => 'Mobile',
            'mini'       => 'Mobile',
        ];

        $device = "Desktop";
        foreach ($checks as $key => $value) {
            $device = strpos($lower, $key) > -1 ? $value : $device;
        }

        return $device;

    }

    public function agentBrowser(string $ua)
    {
        $lower = strtolower($ua);
        $parts = explode(' ', $ua);

        $checks = [
            'safari' => 'Safari',
            'chrome' => 'Chrome',
            'edg'    => 'Edge',
            'opr'    => 'Opera',
        ];

        if (! strpos($lower, ' ')) {
            return $ua;
        }

        $finder = null;
        foreach ($checks as $key => $value) {
            $finder = ! ! strpos($lower, $key) ? $key : $finder;
        }

        if (! $finder) {
            return 'Unkown';
        }

        $browser = $checks[$finder];
        foreach ($parts as $part) {
            if (strpos(strtolower($part), $finder) > -1) {
                $browser = $part;
            }
        }

        return $browser;
    }

}

// Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36
// Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36
// Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36
// Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/150.0.0.0 Mobile/15E148 Safari/604.1
// Mozilla/5.0 (Macintosh; Intel Mac OS X 15_7_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Safari/605.1.15
// Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1
// Mozilla/5.0 (iPad; CPU OS 18_7_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1
// Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0
// Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:152.0) Gecko/20100101 Firefox/152.0
// Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0
// Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)
// Mozilla/5.0 ( Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.4078.99
// Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Chrome/150.0.0.0 Safari/537.36
// Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/150.0.0.0 Safari/537.36
// Mozilla/5.0 (Macintosh; Intel Mac  OS X 15_7_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/133.0.0.0
// PostmanRuntime/7.37.3
// Curl/8.0
// Python-requests/2.31
