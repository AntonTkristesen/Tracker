<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3'
import TextInput from '../Components/TextInput.vue';


const form = useForm({
    email: null,
    password: null,
});

const submit = () => {
    form.post("login", {
        onError: () => form.reset('password'),
    })
};
</script>

<template>
    <Head :title="` | ${$page.component}`" />
    <h1 class="title">Login</h1>

    <div class="container">
        <div class="row">
            <form @submit.prevent="submit" class="col-12">

                <TextInput name="email" v-model="form.email" type="email" :message="form.errors.email"/>  

                <TextInput name="password" v-model="form.password" type="password" :message="form.errors.password"/>  

                <div>
                    <Link :href="route('register')" class="navbar-brand text-decoration-none">Dont have an account?</Link>
                    <button class="btn btn-primary">Login</button>
                </div>
            </form>
        </div>
    </div>
</template>