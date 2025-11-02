@php
    $decodedContent = html_entity_decode($record->content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
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

<div
    x-data="{
        html: @js($decodedContent),
        updatePreview() {
            this.$refs.preview.srcdoc = this.html;
        }
    }"
    x-init="updatePreview()"
    class="space-y-2"
>
    <style>
        .preview-container {
            display: flex;
            gap: 1rem;
            height: 75vh;
        }
        .editor, .preview {
            flex: 1;
            border: 1px solid #444;
            border-radius: 6px;
            background: #111;
            color: #fff;
            display: flex;
            flex-direction: column;
        }
        .editor-header, .preview-header {
            background: #222;
            padding: 6px 10px;
            font-size: 14px;
            font-weight: bold;
            border-bottom: 1px solid #333;
        }
        textarea {
            flex: 1;
            width: 100%;
            background: #0b0c0e;
            color: #eaecef;
            border: none;
            padding: 10px;
            font-family: monospace;
            font-size: 13px;
            outline: none;
            resize: none;
        }
        iframe {
            flex: 1;
            width: 100%;
            border: none;
            background: #fff;
        }
    </style>

    <div class="preview-container">
        <div class="editor">
            <div class="editor-header">HTML Editor</div>
            <textarea
                x-model="html"
                x-on:input.debounce.300ms="updatePreview"
            ></textarea>
        </div>
        <div class="preview">
            <div class="preview-header">Live Preview</div>
            <iframe
                x-ref="preview"
                sandbox="allow-same-origin allow-scripts allow-forms allow-popups allow-modals"
            ></iframe>
        </div>
    </div>
</div>
