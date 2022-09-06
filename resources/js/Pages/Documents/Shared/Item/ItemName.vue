<template>
    <div class="flex gap-4">
        <div v-if="item.image" @click="removeImage" class="group relative flex-none border border-gray-300 shadow-sm rounded-md overflow-hidden">
            <div class="cursor-pointer absolute bg-red-500 opacity-0 group-hover:opacity-80 w-full h-full grid place-items-center transition">
                <x-icon class="text-white w-5 h-5"/>
            </div>
            <img :src="image" class="object-cover w-[42px] h-[42px]"/>
        </div>

        <div class="flex-auto w-full relative">
            <document-item-autocomplete v-model:value="item.name"
                                        :value="item.name"
                                        :currency-symbol="currency"
                                        @itemSelected="$emit('selected', {item, event: $event})"/>

            <jet-input-error :message="error" class="mt-1"/>

            <photograph-icon class="w-6 h-6 p-1 cursor-pointer text-gray-400 hover:text-gray-500 transition-colors absolute outline-none top-2 right-9"
                             v-tooltip="{content: __('Upload image'), triggers: ['hover']}"
                             @click="selectImage"/>

            <pencil-alt-icon class="w-6 h-6 p-1 cursor-pointer text-gray-400 hover:text-gray-500 transition-colors absolute outline-none top-2 right-2"
                             v-tooltip="__('Add description')"
                             @click="toggleDescription"/>
        </div>

        <div class="hidden">
            <input ref="imageUpload" type="file" @change="selectedImage" accept="image/*">
        </div>
    </div>
</template>

<script>
import DocumentItemAutocomplete from "@/Pages/Documents/Shared/Partials/DocumentItemAutocomplete";
import JetInputError from '@/Jetstream/InputError.vue';
import {PencilAltIcon, PhotographIcon, XIcon} from "@heroicons/vue/solid";
import {defineComponent} from "vue";
import Scissor from 'js-scissor';

export default defineComponent({
    name: "ItemName",

    emits: ['selected'],

    components: {
        DocumentItemAutocomplete,
        JetInputError,
        PencilAltIcon,
        PhotographIcon,
        XIcon,
    },

    props: {
        item: {
            type: Object,
        },
        currency: {
            type: String,
        },
        error: {
            type: String,
            default: null,
        },
    },

    computed: {
        image() {
            if (this.item.image && this.item.image.includes('data:image/')) {
                return this.item.image;
            }
            return this.$inertia.page.props.appUrl+'/storage/'+ this.item.image;
        },
    },

    methods: {
        selectImage() {
            this.$refs.imageUpload.click();
        },

        removeImage() {
            this.item.image = null;
            this.$refs.imageUpload.value = null;
        },

        selectedImage(event){
            const file = event.target.files[0];

            const fr = new FileReader();

            fr.onload = async () => {
                this.item.image = (await new Scissor(fr.result)
                    .resize(200, 200, {fit: 'cover'}))
                    .toBase64();
            };
            fr.onerror = () => alert('Error, failed to select image');

            fr.readAsDataURL(file);
        },

        toggleDescription() {
            this.item.descriptionToggled = !this.item.descriptionToggled;

            if (!this.item.descriptionToggled) {
                this.item.description = '';
            }
        },
    },
})
</script>
