<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { onMounted, reactive } from '@vue/runtime-core'; 
import $ from 'jquery';
const props = defineProps(["prodid", "formUrl", "intent", "token", "error", "stripesecret"]);

onMounted(() => {
    
    function includeStripe( URL, callback ) {
        let documentTag = document, tag = 'script',
            object = documentTag.createElement(tag),
            scriptTag = documentTag.getElementsByTagName(tag)[0];
        object.src = '//' + URL;
        if (callback) { object.addEventListener('load', function (e) { callback(null, e); }, false); }
        scriptTag.parentNode.insertBefore(object, scriptTag);
    }
    function configureStripe( URL, callback ) {
        
        const stripe = Stripe(props.stripesecret);
 
        const elements = stripe.elements();
        const cardElement = elements.create('card');
    
        cardElement.mount('#card-element');

        const cardHolderName = document.getElementById('card-holder-name');
        const cardButton = document.getElementById('card-button');
        const clientSecret = cardButton.dataset.secret;
        
        cardButton.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            const { setupIntent, error } = await stripe.confirmCardSetup(
                clientSecret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: { name: cardHolderName.value }
                    }
                }
            );
        
            if (error) {
                // Display "error.message" to the user...
            } else {
                    $('.payment-method').val(setupIntent.payment_method)
                    $('.card-form').submit()
                // The card has been verified successfully...
            }
        });
    }

    includeStripe('js.stripe.com/v3/', function(){
        configureStripe();
    }.bind(this) );
    
    
    
})
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Subscription Payment
            </h2>
        </template>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 d-flex">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="alert alert-error" v-if="props.error">{{props.error}}</div>
                        <form method="POST" :action="props.formUrl" class="card-form mt-3 mb-3">
                            <input type="hidden" name="_token" :value="props.token" />
                            <input type="hidden" name="payment_method" class="payment-method">
                            <input id="card-holder-name" type="text" class="StripeElement mb-3" name="card_holder_name" placeholder="Card holder name" required>
                            <div class="col-lg-4 col-md-6">
                                <div id="card-element"></div>
                            </div>
                            <div id="card-errors" role="alert"></div>
                            <div class="form-group mt-3">
                                <button id="card-button" class="btn btn-primary pay" :data-secret="props.intent.client_secret">
                                    Purchase
                                </button>
                            </div>
                        </form>
                    </div>
                </div>   
            </div>
        </div>
    </AppLayout>
</template>
<style>
    .alert.alert-error{
        background-color: rgb(221, 118, 118);
        padding: 10px 10px;
        color: white;
    }
    .StripeElement {
        box-sizing: border-box;
        height: 40px;
        padding: 10px 12px;
        border: 1px solid transparent;
        border-radius: 4px;
        background-color: white;
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }
    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }
    .StripeElement--invalid {
        border-color: #fa755a;
    }
    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
    .card-form{
        border: 2px solid lightgray;
        padding: 20px;
        border-radius: 10px;
    }
    .btn-primary{
        background: rgb(16, 115, 235);
        color: white;
        padding: 2px 10px;
        
    }
</style>
