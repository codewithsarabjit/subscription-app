<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PricingCard from '@/Jetstream/PricingCard.vue';
const props = defineProps({
    products: {
        type: Array,
        default: [],
    },
    prices: {
        type: Array,
        default: [],
    },
    subscription: {
        type: Object,
        default: {},
    }
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Subscription Plans
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 d-flex">
                <PricingCard v-for="(product, pKey) in props.products" :key="pKey" :product="product" :price="props.prices.filter((p)=>p.product===product.id)[0]" :subscription="subscription" />    
            </div>
        </div>
    </AppLayout>
</template>
