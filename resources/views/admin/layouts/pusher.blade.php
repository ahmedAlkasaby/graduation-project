<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

<script>
    Pusher.logToConsole = false;
    try {
        const pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
        const pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}';

        var pusher = new Pusher(pusherKey, {
            cluster: pusherCluster,
            forceTLS: true,
            enabledTransports: ['ws', 'wss'],
            disableStats: false
        });

        {{-- Shared toast for realtime events. Subscribe to a channel and call
             showToastr(title, message, url) from its handler. --}}
        function showToastr(title, message, url = null) {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 5000,
                onclick: function() {
                    if (url) {
                        window.location.href = url;
                    }
                }
            };
            toastr.info(message, title);
        }
    } catch (error) {
        console.error('Error in Pusher setup:', error);
    }
</script>
