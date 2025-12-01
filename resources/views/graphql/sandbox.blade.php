<body style="margin: 0;">
    <div style="width: 100%; height: 100%;" id='embedded-sandbox'></div>
    <script src="https://embeddable-sandbox.cdn.apollographql.com/v2/embeddable-sandbox.umd.production.min.js"></script> 
    <script>
        new window.EmbeddedSandbox({
            target: '#embedded-sandbox',
            initialEndpoint: '{{ $endpoint }}',
        });
    </script>
</body>