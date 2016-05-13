var nitro_pricing;
var nitro_ids;

(function($){

    $(function(){

        $('#nitro-k9-pricing-submit').click(function(e){

            e.preventDefault();
            nitro_pricing = {};
            nitro_ids = [];

            $('#nitro-k9-pricing-form-fields').find('input, select').each(function(){

                var name = $(this).attr('name');
                var parts = name.split('_');
                var id = parts[2];

                if (parts[0] == 'pg') {
                    if (parts[1] == 'active') {
                        nitro_pricing[id] = {
                            is_active: $(this).val(),
                            title: '',
                            prices: []
                        };
                        nitro_ids.push(id);
                    } else if (parts[1] == 'title') {
                        nitro_pricing[id].title = $(this).val()
                    }
                } else if (parts[0] == 'p') {
                    var index = parseInt(parts[3]);
                    if (nitro_pricing[id].prices.length == index) {
                        nitro_pricing[id].prices.push({
                            is_active: nitro_pricing[id].is_active,
                            title: '',
                            price: ''
                        });
                    }
                    if (parts[1] == 'active') {
                        if (nitro_pricing[id].is_active == '0'){
                            nitro_pricing[id].prices[index].is_active = '0';
                        } else {
                            nitro_pricing[id].prices[index].is_active = $(this).val();
                        }
                    } else if (parts[1] == 'title') {
                        nitro_pricing[id].prices[index].title = $(this).val();
                    } else if (parts[1] == 'price') {
                        nitro_pricing[id].prices[index].price = $(this).val().replace(/[^0-9.]/g, '');
                    }
                }

            });

            for (var x=0; x<nitro_ids.length; x++){
                var id = nitro_ids[x];
                if (nitro_pricing[id].is_active == '1'){
                    delete nitro_pricing[id].is_active;
                }
                if (nitro_pricing[id].prices.length == 1){
                    nitro_pricing[id].price = nitro_pricing[id].prices[0].price;
                    delete nitro_pricing[id].prices;
                } else {
                    for (var y=0; y<nitro_pricing[id].prices.length; y++){
                        if (nitro_pricing[id].prices[y].is_active == '1'){
                            delete nitro_pricing[id].prices[y].is_active;
                        }
                    }
                }
            }

            console.log(nitro_pricing);

            $('#nitro-k9-pricing').val(JSON.stringify(nitro_pricing));
            $('#nitro-k9-pricing-form').submit();

        });

    });

})(jQuery);