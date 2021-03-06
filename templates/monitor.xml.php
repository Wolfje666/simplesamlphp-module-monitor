<?php

use sspmod_monitor_State as State;

$modules = $this->data['modules'];
$configuration = $this->data['configuration'];
$store = $this->data['store'];
$state = $this->data['overall'];
$authsources = $this->data['authsources'];
$metadata = $this->data['metadata'];
$health_info = $this->data['health_info'];

$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

list($health_state, $health_color) = $health_info[$state];
$overall = $health_state;

if ($state === State::OK) {
  header($protocol . ' 200 OK');
  $GLOBALS['http_response_code'] = 200;
} else if ($state === State::WARNING) {
  header($protocol . ' 417 Expectation failed');
  $GLOBALS['http_response_code'] = 417;
} else {
  header($protocol . ' 500 Internal Server Error');
  $GLOBALS['http_response_code'] = 500;
}

$output = '<?xml version="1.0" encoding="UTF-8"?>';
$output .= '<monitor>';
$output .= '<health>' . $overall . '</health>';
$output .= '<checks>';

foreach ($modules as $check) {
    list($health, $category, $subject, $summary) = $check;
    list($health_state, $health_color) = $health_info[$health];

    $output .= '<check category="' . $category . '">';
    $output .= '<subject>' . $subject . '</subject>';
    $output .= '<health>' . $health_state . '</health>';
    $output .= '<summary>' . $summary . '</summary>';
    $output .= '</check>';
}

foreach ($configuration as $check) {
    list($health, $category, $subject, $summary) = $check;
    list($health_state, $health_color) = $health_info[$health];

    $output .= '<check category="' . $category . '">';
    $output .= '<subject>' . $subject . '</subject>';
    $output .= '<health>' . $health_state . '</health>';
    $output .= '<summary>' . $summary . '</summary>';
    $output .= '</check>';
}

foreach ($store as $check) {
    list($health, $category, $subject, $summary) = $check;
    list($health_state, $health_color) = $health_info[$health];

    $output .= '<check category="' . $category . '">';
    $output .= '<subject>' . $subject . '</subject>';
    $output .= '<health>' . $health_state . '</health>';
    $output .= '<summary>' . $summary . '</summary>';
    $output .= '</check>';
}

foreach ($authsources as $name => $authsource) {
    foreach ($authsource as $check) {
        list($health, $category, $subject, $summary) = $check;
        list($health_state, $health_color) = $health_info[$health];

        $output .= '<check category="' . $category . '">';
        $output .= '<subject>' . $subject . '</subject>';
        $output .= '<health>' . $health_state . '</health>';
        $output .= '<summary>' . $summary . '</summary>';
        $output .= '</check>';
    }
}

foreach ($metadata as $entityId => $entity_metadata) {
    foreach ($entity_metadata as $check) {
        list($health, $category, $subject, $summary) = $check;
        list($health_state, $health_color) = $health_info[$health];

        $output .= '<check category="' . $category . '">';
        $output .= '<subject>' . $subject . '</subject>';
        $output .= '<health>' . $health_state . '</health>';
        $output .= '<summary>' . $summary . '</summary>';
        $output .= '</check>';
    }
}

$output .= "</checks>";
$output .= "</monitor>";

header("Content-Type: text/xml");
echo $output;
