<?php
include '../../vendor/autoload.php';

// #https://github.com/rollerworks/version
use Rollerworks\Component\Version\Version;
use Rollerworks\Component\Version\VersionsValidator;

$sentryUrl = getenv("SENTRY_URL");
if (isset($sentryUrl)) {
    Sentry\init([
        'dsn' => $sentryUrl
    ]);
}
if (! isset($_GET['up'])) {
    echo 'missing up parameter';
    exit();
}
$up = $_GET['up'];
if (! in_array($up, [
    'major',
    'minor',
    'patch',
    'next',
    'patch',
    'stable',
    'alpha',
    'beta',
    'rc'
])) {
    echo 'wrong up parameter';
    exit();
}
$version = $_GET['version'];

if (! preg_match('/^v?' . Rollerworks\Component\Version\Version::VERSION_REGEX . '$/i', $version, $matches)) {
    echo 'wrong version';
    exit();
}
$now = time();
$then = gmstrftime("%a, %d %b %Y %H:%M:%S GMT", $now + 365 * 86440);
header("Expires: $then");
$etag = md5($version);
header("Etag: $etag");
header('Cache-Control: public, max-age=864000');

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
    header("HTTP/1.1 304 Not Modified");
    exit();
}

$version = Version::fromString($version);

$newVersion = $version->getNextIncreaseOf($up);

echo $newVersion;

