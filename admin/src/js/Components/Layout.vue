<template>
    <div class="mt-5 location-domination-vendor">
        <div style="max-width: 1240px;" class="w-full container mx-auto">
            <div v-if="!isAccountConnected" role="alert" class="rounded-lg mb-12  warning bg-warning text-orange-700 p-4">
                <div class="flex p-4">
                    <div class="block ml-8 mt-2">
                        <h2>You have not connected your Location Domination account</h2>
                        <p class="mt-2">In order to access
                            <strong>all</strong> of the features that we offer within this plugin, please connect your account.
                        </p>
                        <p class="mt-5">
                            <router-link to="/account" class="fancy">Connect my account</router-link>
                        </p>
                    </div>
                </div>
            </div>

            <div v-else role="alert" class="rounded-lg mb-12 bg-green-200 text-green-700 p-4">
                <div class="flex p-4">
                    <div class="block ml-8">
                        <h2>You have connected your account successfully.</h2>
                        <p class="mt-2">Thank you for connecting your account. Enjoy our wide range of features.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded border border-gray-300 mx-auto bg-white flex" style="max-width: 1240px;">
            <div class="w-1/5 border-r border-gray-300">
                <router-link to="/dashboard">
                    <svg
                            class="block mx-auto mt-8 mb-2" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink" width="151.991" height="39.864"
                            viewBox="0 0 151.991 39.864">
                        <defs>
                            <radialGradient
                                    id="radial-gradient" cx="0.5" cy="0.5" r="0.394"
                                    gradientTransform="translate(-0.514) scale(2.027 1)"
                                    gradientUnits="objectBoundingBox">
                                <stop offset="0" stop-color="#848b92" />
                                <stop offset="0.516" stop-color="#828990" />
                                <stop offset="0.704" stop-color="#7b8289" />
                                <stop offset="0.838" stop-color="#6f777d" />
                                <stop offset="0.946" stop-color="#5e676c" />
                                <stop offset="1" stop-color="#535d61" />
                            </radialGradient>
                            <radialGradient
                                    id="radial-gradient-2" r="0.501" gradientTransform="translate(0.001)"
                                    xlink:href="#radial-gradient" />
                            <radialGradient
                                    id="radial-gradient-3" r="0.485"
                                    gradientTransform="translate(-0.033) scale(1.065 1)"
                                    xlink:href="#radial-gradient" />
                            <radialGradient
                                    id="radial-gradient-4" r="0.473"
                                    gradientTransform="translate(-0.061) scale(1.123 1)"
                                    xlink:href="#radial-gradient" />
                            <radialGradient
                                    id="radial-gradient-5" r="0.4"
                                    gradientTransform="translate(-0.444) scale(1.888 1)"
                                    xlink:href="#radial-gradient" />
                            <radialGradient
                                    id="radial-gradient-6" r="0.357"
                                    gradientTransform="translate(-2.926) scale(6.852 1)"
                                    xlink:href="#radial-gradient" />
                            <radialGradient
                                    id="radial-gradient-8" r="0.452"
                                    gradientTransform="translate(-0.129) scale(1.258 1)"
                                    xlink:href="#radial-gradient" />
                            <radialGradient
                                    id="radial-gradient-9" r="0.461"
                                    gradientTransform="translate(-0.099) scale(1.197 1)"
                                    xlink:href="#radial-gradient" />
                            <radialGradient
                                    id="radial-gradient-10" r="0.515"
                                    gradientTransform="translate(0.029) scale(0.943 1)"
                                    xlink:href="#radial-gradient" />
                            <radialGradient
                                    id="radial-gradient-12" r="0.452"
                                    gradientTransform="translate(-0.129) scale(1.259 1)"
                                    xlink:href="#radial-gradient" />
                            <radialGradient
                                    id="radial-gradient-18" r="0.501" gradientTransform="translate(0.002)"
                                    xlink:href="#radial-gradient" />
                            <linearGradient
                                    id="linear-gradient" y1="0.5" x2="1" y2="0.5"
                                    gradientUnits="objectBoundingBox">
                                <stop offset="0" stop-color="#eec955" />
                                <stop offset="0.045" stop-color="#ebc552" />
                                <stop offset="0.535" stop-color="#cfa138" />
                                <stop offset="0.798" stop-color="#c5942f" />
                            </linearGradient>
                        </defs>
                        <g id="svgexport-0" transform="translate(-274.727 -453.258)">
                            <path
                                    id="Path_1" data-name="Path 1" d="M317.752,229.1v-17.93h2.617V226.7H326.6v2.4Z"
                                    transform="translate(-42.899 242.434)" fill="url(#radial-gradient)" />
                            <path
                                    id="Path_2" data-name="Path 2"
                                    d="M348.067,229.04a9.3,9.3,0,1,1,9.341-9.267,9.339,9.339,0,0,1-9.341,9.267Zm6.856-9.256a6.715,6.715,0,1,0-6.711,6.855A6.772,6.772,0,0,0,354.923,219.784Z"
                                    transform="translate(-54.268 242.829)" fill="url(#radial-gradient-2)" />
                            <path
                                    id="Path_3" data-name="Path 3"
                                    d="M400.6,224.263a9.161,9.161,0,0,1-8.286,4.773,9.389,9.389,0,0,1-9.062-9.231,9.278,9.278,0,0,1,17.469-4.458h-3.149a6.393,6.393,0,0,0-5.04-2.52,6.752,6.752,0,0,0-6.664,6.734c0,3.756,2.835,7.075,6.518,7.075a6.418,6.418,0,0,0,4.99-2.375Z"
                                    transform="translate(-78.137 242.833)" fill="url(#radial-gradient-3)" />
                            <path
                                    id="Path_4" data-name="Path 4"
                                    d="M434.026,229.1l-1.987-5.282h-6.445l-1.987,5.282h-2.762l6.808-17.93H430l6.808,17.93Zm-5.163-14.538h-.046l-2.3,6.857h4.628Z"
                                    transform="translate(-98.364 242.434)" fill="url(#radial-gradient-4)" />
                            <path
                                    id="Path_5" data-name="Path 5"
                                    d="M458.528,229.1V213.565h-3.441v-2.4h9.5v2.4h-3.44V229.1Z"
                                    transform="translate(-116.786 242.434)" fill="url(#radial-gradient-5)" />
                            <path
                                    id="Path_6" data-name="Path 6" d="M480,229.1v-17.93h2.617V229.1Z"
                                    transform="translate(-130.189 242.434)" fill="url(#radial-gradient-6)" />
                            <path
                                    id="Path_7" data-name="Path 7"
                                    d="M500.715,229.04a9.3,9.3,0,1,1,9.341-9.267,9.339,9.339,0,0,1-9.341,9.267Zm6.857-9.256a6.715,6.715,0,1,0-6.711,6.855A6.772,6.772,0,0,0,507.572,219.784Z"
                                    transform="translate(-136.394 242.829)" fill="url(#radial-gradient-2)" />
                            <path
                                    id="Path_8" data-name="Path 8"
                                    d="M549.372,229.1l-8.867-13.86h-.046V229.1h-2.617v-17.93H540.7l8.723,13.641h.046V211.166h2.617V229.1Z"
                                    transform="translate(-161.308 242.434)" fill="url(#radial-gradient-8)" />
                            <g id="Group_2" data-name="Group 2" transform="translate(274.727 473.451)">
                                <path
                                        id="Path_9" data-name="Path 9"
                                        d="M591.479,229.1v-17.93h6.227a8.649,8.649,0,0,1,8.746,8.963c0,5.476-3.465,8.963-9.134,8.963Zm5.742-2.4c4.143,0,6.615-2.423,6.615-6.518a6.292,6.292,0,0,0-6.494-6.614H594.1V226.7Z"
                                        transform="translate(-591.479 -209.764)" fill="url(#radial-gradient-9)" />
                                <path
                                        id="Path_10" data-name="Path 10"
                                        d="M691.154,229.1V213.954h-.072L685.048,229.1h-1.575l-6.057-15.142h-.048V229.1H674.75V211.166h4.12l5.379,13.737,5.4-13.737h4.119V229.1Z"
                                        transform="translate(-636.279 -209.764)" fill="url(#radial-gradient-10)" />
                                <path
                                        id="Path_11" data-name="Path 11" d="M723.728,229.1v-17.93h2.617V229.1Z"
                                        transform="translate(-662.63 -209.764)" fill="url(#radial-gradient-6)" />
                                <path
                                        id="Path_12" data-name="Path 12"
                                        d="M748.735,229.1l-8.868-13.86h-.049V229.1H737.2v-17.93h2.859l8.722,13.641h.048V211.166h2.617V229.1Z"
                                        transform="translate(-669.878 -209.764)" fill="url(#radial-gradient-12)" />
                                <path
                                        id="Path_13" data-name="Path 13"
                                        d="M785.833,229.1l-1.987-5.282H777.4l-1.987,5.282h-2.762l6.809-17.93h2.35l6.808,17.93Zm-5.161-14.538h-.049l-2.3,6.857h4.627Z"
                                        transform="translate(-688.952 -209.764)" fill="url(#radial-gradient-4)" />
                                <path
                                        id="Path_14" data-name="Path 14"
                                        d="M810.334,229.1V213.565h-3.44v-2.4h9.5v2.4h-3.44V229.1Z"
                                        transform="translate(-707.373 -209.764)" fill="url(#radial-gradient-5)" />
                                <path
                                        id="Path_15" data-name="Path 15" d="M831.8,229.1v-17.93h2.617V229.1Z"
                                        transform="translate(-720.773 -209.764)" fill="url(#radial-gradient-6)" />
                                <path
                                        id="Path_16" data-name="Path 16"
                                        d="M852.521,229.04a9.3,9.3,0,1,1,9.341-9.268,9.339,9.339,0,0,1-9.341,9.268Zm6.857-9.256a6.715,6.715,0,1,0-6.712,6.855A6.772,6.772,0,0,0,859.378,219.784Z"
                                        transform="translate(-726.981 -209.369)" fill="url(#radial-gradient-2)" />
                                <path
                                        id="Path_17" data-name="Path 17"
                                        d="M901.169,229.1l-8.87-13.86h-.046V229.1h-2.617v-17.93h2.859l8.722,13.641h.046V211.166h2.617V229.1Z"
                                        transform="translate(-751.889 -209.764)" fill="url(#radial-gradient-12)" />
                                <g id="Group_1" data-name="Group 1" transform="translate(16.908 0)">
                                    <path
                                            id="Path_18" data-name="Path 18"
                                            d="M646.593,218.337a.92.92,0,0,0-.682.191.957.957,0,0,0-.36.65,8.007,8.007,0,0,1-.693,2.546,7.818,7.818,0,0,1-10.312,3.829c.262-.152.539-.329.828-.531a2.515,2.515,0,0,0,.693-.757l.074-.131a3.918,3.918,0,0,0,.416-.918,2.439,2.439,0,0,0-1.26-3.1c-.077-.046-.176-.107-.236-.151.051-.2.365-.468.623-.689.141-.121.281-.241.4-.362a1.894,1.894,0,0,0,.443-2.146,1.744,1.744,0,0,0-1.912-.934,2.21,2.21,0,0,0-1.709,1.922,3.755,3.755,0,0,1-.942,1.961.388.388,0,0,1-.358.082.6.6,0,0,1-.439-.37c-.046-.125-.023-.192.166-.477a1.817,1.817,0,0,0,.385-1.365c-.026-.209-.178-.709-1.031-.832l-.039-.006-.555.016a7.638,7.638,0,0,1,.55-1.639,7.761,7.761,0,0,1,6.144-4.455.936.936,0,0,0-.035-1.861.822.822,0,0,0-.185,0,9.48,9.48,0,0,0-7.59,5.493,9.719,9.719,0,0,0,8.789,13.809,9.966,9.966,0,0,0,3.331-.586,9.577,9.577,0,0,0,5.474-5.021,9.936,9.936,0,0,0,.872-3.163A.944.944,0,0,0,646.593,218.337Z"
                                            transform="translate(-628.077 -208.497)" fill="url(#radial-gradient-18)" />
                                    <path
                                            id="Path_19" data-name="Path 19"
                                            d="M651.491,219.547a1.269,1.269,0,0,0,.988.462,1.323,1.323,0,0,0,.988-.462c1.323-1.61,3.557-4.639,3.475-6.951a4.464,4.464,0,1,0-8.927,0C647.952,214.909,650.168,217.953,651.491,219.547Zm.988-8.529a1.594,1.594,0,1,1-1.594,1.594A1.594,1.594,0,0,1,652.479,211.018Z"
                                            transform="translate(-638.803 -208.132)" fill="url(#linear-gradient)" />
                                </g>
                            </g>
                        </g>
                    </svg>
                </router-link>

                <p class="text-sm text-center text-gray-600">version {{ version }}</p>

                <MainMenu></MainMenu>
            </div>

            <div style="background:#FEFEFE" class="w-4/5 py-8 px-12">
                <router-view v-bind="$attrs" />
            </div>
        </div>
    </div>
</template>

<script>
    import MainMenu from './MainMenu.vue';
    import {mapGetters, mapMutations} from 'vuex';

    export default {
        name: 'Layout',
        props: ['version', 'ajaxUrl', 'nonce', 'connected'],
        components: { MainMenu },
        computed: {
            ...mapGetters( ['isAccountConnected'] )
        },

        mounted() {
            this.setAccountConnected( this.connected );
            document.body.classList.add( 'location-domination' );
        },

        methods: {
            ...mapMutations( ['setAccountConnected'] )
        }
    };
</script>

<style lang="scss">
    @import "../../scss/index.scss";

    body.location-domination {
        .wp-core-ui input,
        .wp-core-ui select {
            min-height: auto !important;
            padding: 2rem 1.25rem 0.5rem 1.25rem !important;
            border-color: #cbd5e0 !important;
            max-width: 100% !important;
        }

        body, html {
            font-size: 15px !important;
        }

        .text-dark {
            color: #3A2E39;
        }

        .text-grey {
            color: rgba(58, 46, 56, 0.47);
        }

        .bg-warning {
            background-color: #f9efda;
        }

        div[role=alert], div[role=alert] p {
            color: #8b8b8b;
            font-size: 15px;
            line-height: 1.5;
        }

        .strong,
        h1, h2, h3, h4, h5, h6 {
            color: #0a1541 !important;
            font-weight: 700;
        }

        a.fancy, .dashboard-heading a {
            font-weight: 600;
            border-bottom: 1px solid #155dec;
            padding-bottom: 1px;
            position: relative;
            top: -4px;
            color: #155dec;
        }
    }
</style>