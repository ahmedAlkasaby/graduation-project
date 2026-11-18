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


        var channel_order = pusher.subscribe('order-channel');
        channel_order.bind('request-order', function(data) {
            var sound = new Audio("{{ asset('js/sharp.mp3') }}");
            sound.play();

            var order_text = "New Order";
            var language = $('body').attr('data-language');
            if (language == "ar") {
                order_text = "طلب جديد";
            }

            var orderUrl = "/admin/orders/" + data.id;

            showToastr(order_text, 'Order #' + data.id, orderUrl);
        });
        
        var channel_order_item_return = pusher.subscribe('order-item-return-channel');
        channel_order_item_return.bind('order-item-return', function(data) {
            var sound = new Audio("{{ asset('js/sharp.mp3') }}");
            sound.play();

            var order_text = "New Order";
            var language = $('body').attr('data-language');
            if (language == "ar") {
                order_text = "طلب إرجاع جديد";
            }

            var orderUrl = "/admin/order_returns/";

            showToastr(order_text, 'Order Item Return #' + data.id, orderUrl);
        });

        var channel_product = pusher.subscribe('product-channel');
        channel_product.bind('request-product', function(data) {
            var sound = new Audio("{{ asset('js/sharp.mp3') }}");
            sound.play();

            var product_text = "New Product";
            var language = $('body').attr('data-language');
            if (language == "ar") {
                product_text = " كميه المنتج قاربت علي الانتهاء" ;
            }

            var productUrl = "/admin/products/" + data.id + "/edit";

            showToastr(product_text, 'Product #' + data.id, productUrl);
        });


        var channel_contact = pusher.subscribe('contact-channel');
        channel_contact.bind('request-contact', function(data) {
            var contact_text = "New Contact";
            var language = $('body').attr('data-language');
            if (language == "ar") {
                contact_text = "اتصال جديد";
            }
            showToastr(contact_text, 'Contact #' + data.id);
        });

    } catch (error) {
        console.error('Error in Pusher setup:', error);
    }
</script>
