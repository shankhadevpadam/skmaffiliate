@php
    $decodedContent = html_entity_decode($template->content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    // Remove wrapping <p> tags that break the HTML structure
    $decodedContent = preg_replace('/<\/?p>/i', '', $decodedContent);
    // Clean up any extra whitespace
    $decodedContent = trim($decodedContent);

    // Add CSP meta tag to allow external images if not already present
    if (!str_contains(strtolower($decodedContent), 'content-security-policy')) {
        $decodedContent = str_replace(
            '<head>',
            '<head><meta http-equiv="Content-Security-Policy" content="default-src \'self\' \'unsafe-inline\' data: https:; img-src * data: https:;">',
            $decodedContent
        );
    }
@endphp

{!! $decodedContent !!}