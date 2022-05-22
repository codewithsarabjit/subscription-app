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
    },
    success: {
        type: String,
        default: null,
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
            <div class="alert alert-success" v-if="props.success">{{props.success}}</div>
                <PricingCard v-for="(product, pKey) in props.products" :key="pKey" :product="product" :price="props.prices.filter((p)=>p.product===product.id)[0]" :subscription="subscription" />    
            </div>
        </div>
    </AppLayout>
</template>
<style scoped>
    .alert.alert-success{
        background-color: rgb(126, 193, 133);
        padding: 10px 10px;
        color: white;
    }
</style>