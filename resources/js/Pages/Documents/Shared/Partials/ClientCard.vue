<template>
    <jet-card class="col-span-6 lg:col-span-2" :class="$attrs.class" :padding="false">
        <template #title>
            <div class="flex justify-between">
                <div>{{ __('Client') }}</div>
                <plus-circle-icon @click="modal.addClient = true"
                                  v-tooltip="{content: __('Add client'), delay: {show: 1000}}"
                                  class="w-6 h-6 text-gray-400 hover:text-indigo-500 transition-colors cursor-pointer -mr-2 focus:outline-none"/>
            </div>
        </template>

        <div class="pt-0 p-6">
            <div class="flex justify-between">
                <jet-label for="inputClient" :value="__('Customer')" required/>
                <div v-if="form.client_id"
                     class="font-medium text-indigo-600 hover:text-indigo-500 hover:underline text-sm cursor-pointer"
                     @click="modal.editClient = form.client_id">
                    {{ __('Edit client') }}
                </div>
            </div>

            <jet-dropdown-select-filter ref="clientDropdown" id="inputClient" class="mt-1 w-full" :options="clientsDropdownItems" v-model:value="form.client_id"/>
            <jet-input-error :message="form.errors.client_id" v-if="form.client_id === null" class="mt-2"/>
        </div>

        <selected-client-preview v-if="form.client_id"
                                 :client="form.client"
                                 :except="['name']"
                                 class="mt-2"/>
    </jet-card>

    <add-client-modal v-if="modal.addClient"
                      :show="modal.addClient"
                      :country-dropdown-items="countryDropdownItems"
                      @close="modal.addClient = false"
                      @set-client="$emit('set-client', $event)"/>

    <edit-client-modal v-if="modal.editClient !== false"
                       :show="modal.editClient !== false"
                       :client="client"
                       :country-dropdown-items="countryDropdownItems"
                       @close="modal.editClient = false"
                       @set-client="$emit('set-client', $event)"/>
</template>

<script>
import SelectedClientPreview from "@/Pages/Documents/Shared/Partials/SelectedClientPreview";
import AddClientModal from "@/Pages/Modals/Client/AddClient";
import EditClientModal from "@/Pages/Modals/Client/EditClient";
import JetCard from "@/Jetstream/Card";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetDropdownSelectFilter from "@/Jetstream/DropdownSelectFilter";
import {PlusCircleIcon} from '@heroicons/vue/solid';

export default {
    name: "ClientCard",

    emits: ['set-client'],

    components: {
        JetDropdownSelectFilter,
        JetLabel,
        JetCard,
        JetInputError,
        EditClientModal,
        AddClientModal,
        SelectedClientPreview,
        PlusCircleIcon,
    },

    props: ['form', 'clients', 'countryDropdownItems', 'clientsDropdownItems'],

    data() {
        return {
            modal: {
                addClient: false,
                editClient: false,
            },
        }
    },

    watch: {
        'form.client_id'(value) {
            if (value === null) {
                this.$refs.clientDropdown.selection = null;
            }
        },
    },

    computed: {
        client() {
            return this.clients.find(i => i.id === this.form.client_id);
        },
    },
}
</script>
