<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PricingCard from '@/Jetstream/PricingCard.vue';
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
                <PricingCard :price="0" />                    
                <PricingCard :price="29.99" />                  
                <PricingCard :price="59.99" />  
            </div>
        </div>
    </AppLayout>
</template>
