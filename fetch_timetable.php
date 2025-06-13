<?php
// URL of the timetable page
$url = 'https://www.grauzonefestival.nl/timetable#/2025-02-07';

// Fetch the HTML content
$html = file_get_contents($url);

// Suppress warnings from malformed HTML
libxml_use_internal_errors(true);

// Load HTML into DOMDocument
$dom = new DOMDocument();
$dom->loadHTML($html);

// Create DOMXPath for querying
$xpath = new DOMXPath($dom);

// Find all location titles
$locations = $xpath->query('//hgroup[contains(@class, "Location-Titles")]');

$timetable = [];

foreach ($locations as $location) {
    $locationName = trim($location->textContent);

    // Find the next sibling (which may contain events)
    $next = $location->nextSibling;
    while ($next && $next->nodeType !== XML_ELEMENT_NODE) {
        $next = $next->nextSibling;
    }

    $events = [];
    if ($next) {
        // Example: get all event rows under this location
        foreach ($next->getElementsByTagName('div') as $eventDiv) {
            $eventText = trim($eventDiv->textContent);
            if ($eventText) {
                $events[] = $eventText;
            }
        }
    }

    $timetable[] = [
        'location' => $locationName,
        'events' => $events
    ];
}

// Output as JSON
header('Content-Type: application/json');
echo json_encode($timetable, JSON_PRETTY_PRINT);
?>
