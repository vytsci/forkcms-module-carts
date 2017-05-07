jsFrontend.Carts.Cart = {
    init: function () {
        jsFrontend.Carts.Cart.initControlAddItem();
        jsFrontend.Carts.Cart.initControlRemoveItem();
    },
    initControlAddItem: function () {
        $(document).on('click', '.jsCartsCartAddItem', function (e) {
            e.preventDefault();

            var $this = $(this);
            var $parent = $this.closest($this.data('parent'));
            var externalId = $parent.find($this.data('input-external-id')).val();
            var module = $parent.find($this.data('input-module')).val();
            var quantity = $parent.find($this.data('input-quantity')).val();
            var options = {};
            $parent.find($this.data('input-options')).each(function () {
                var name = $(this).attr('name').match(/[A-Za-z0-9_]+\[(.*)\]/i);
                if (typeof name[1] != 'undefined') {
                    options[name[1]] = $(this).val();
                }
            });

            $.ajax({
                data: {
                    fork: {module: 'Carts', action: 'AddItem'},
                    external_id: externalId,
                    module: module,
                    quantity: quantity,
                    options: options
                },
                success: function (response, textStatus) {
                    if (typeof response.data.error != 'undefined') {
                        switch (response.data.error) {
                            case 'login':
                                alert(response.message);
                                break;
                            case 'invalid':
                                alert(response.message);
                                break;
                            case 'parameters':
                                alert(response.message);
                                break;
                            default:
                                alert(response.message);
                        }

                        return;
                    }

                    if (response.data.html.length > 0) {
                        $('.jsCartsCart').replaceWith(response.data.html);
                    }

                    if (response.data.message.length > 0) {
                        $this.tooltip({
                            title: response.data.message
                        });
                        $this.tooltip('show');
                    }

                    $(document).trigger('carts_after_add_item', response.data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(jsFrontend.locale.lbl('SomethingWentWrong'));
                }
            });
        });
    },
    initControlRemoveItem: function () {
        $(document).on('click', '.jsCartsCartRemoveItem', function (e) {
            e.preventDefault();

            var $this = $(this);

            $.ajax({
                data: {
                    fork: {module: 'Carts', action: 'RemoveItem'},
                    id: $this.data('cart-item')
                },
                success: function (response, textStatus) {
                    if (typeof response.data.error != 'undefined') {
                        switch (response.data.error) {
                            case 'login':
                                alert(response.message);
                                break;
                            case 'parameters':
                                alert(response.message);
                                break;
                            default:
                                alert(response.message);
                        }

                        return;
                    }

                    if (response.data.html.length > 0) {
                        $('.jsCartsCart').replaceWith(response.data.html);
                    }

                    $(document).trigger('carts_after_remove_item', response.data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(jsFrontend.locale.lbl('SomethingWentWrong'));
                }
            });
        });
    }
};

$(jsFrontend.Carts.Cart.init);
