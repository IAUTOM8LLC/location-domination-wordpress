<template>
    <div class="location-domination-vendor mt-8 mb-6">
        <router-link to="/dashboard" class="router-link w-full pl-8 pr-4 flex py-3">
                <span style="width: 22px;" class="block">
                    <svg class="mx-auto inline-block" xmlns="http://www.w3.org/2000/svg" width="26.287" height="20.445" viewBox="0 0 26.287 20.445">
                        <path d="M26.051,41.852l-2.658-2.186V35.307a.365.365,0,0,0-.365-.365h-1.46a.365.365,0,0,0-.363.36v2.566l-6.4-5.253a2.558,2.558,0,0,0-3.274,0L.29,41.852A.73.73,0,0,0,.2,42.88l.467.561a.73.73,0,0,0,1.028.091L2.948,42.5v8.5a1.465,1.465,0,0,0,1.46,1.46H10.25a1.464,1.464,0,0,0,1.46-1.46V46.26h2.921v4.746a1.464,1.464,0,0,0,1.46,1.46h5.842a1.464,1.464,0,0,0,1.46-1.449V42.5l1.256,1.032a.73.73,0,0,0,1.028-.091l.467-.561a.73.73,0,0,0-.094-1.031ZM21.2,50.276H16.822V45.53a1.465,1.465,0,0,0-1.46-1.46H10.98a1.465,1.465,0,0,0-1.46,1.46v4.746H5.139V40.7l8.032-6.6L21.2,40.7Z" transform="translate(-0.028 -32.022)" fill="#a29ca1" />
                    </svg>
                </span>
            <span class="block ml-5 leading-normal">Dashboard</span>
        </router-link>

        <router-link to="/account" class="router-link w-full pl-8 pr-4 flex py-3">
                <span style="width: 22px;" class="block">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19.25" height="22" viewBox="0 0 19.25 22"><path d="M13.475,13.063c-1.233,0-1.826.688-3.85.688s-2.613-.687-3.85-.687A5.776,5.776,0,0,0,0,18.837v1.1A2.063,2.063,0,0,0,2.063,22H17.188a2.063,2.063,0,0,0,2.063-2.062v-1.1A5.776,5.776,0,0,0,13.475,13.063Zm3.712,6.875H2.063v-1.1a3.719,3.719,0,0,1,3.712-3.712c.627,0,1.646.688,3.85.688,2.221,0,3.218-.687,3.85-.687a3.719,3.719,0,0,1,3.712,3.712ZM9.625,12.375A6.188,6.188,0,1,0,3.438,6.188,6.189,6.189,0,0,0,9.625,12.375Zm0-10.312A4.125,4.125,0,1,1,5.5,6.188,4.132,4.132,0,0,1,9.625,2.063Z" fill="#a29ca1"/></svg>
                </span>
            <span class="block ml-5 leading-normal">My Account</span>
        </router-link>
    </div>
</template>

<script>
    export default {
        name: "MainMenu",

        data() {
            return {
            }
        }
    }
</script>

<style lang="scss" scoped>
    .router-link {
        font-weight: 600;
        font-size: 16px;
        color: #A7A7A7;

        &.router-link-active {
            border-right: 2px solid #EBC552;
            font-weight: 700;
            color: #3A2E39;
        }
    }
</style>