<template>
    <div v-if="!request.started">
<!--        <advanced-select-input-->
<!--                v-model="gridForm.country"-->
<!--                @input="updateRegions"-->
<!--                :close-on-select="true" track-by="id" :options="countries"-->
<!--                label-key="name" label="Select a country" />-->

<!--        <template v-if="gridForm.country && gridForm.country.id === 236">-->
            <select-input name="requestType" v-model="gridForm.group" label="How would you like to build posts?">
                <option v-for="option in groupOptions">
                    {{ option }}
                </option>
            </select-input>

            <advanced-select-input
                    name="states[]" @input="updateCounties" v-model="gridForm.states"
                    v-if="gridForm.group && gridForm.group !== 'For all cities/counties'"
                    :close-on-select="false" :multiple="true" track-by="id" :options="states"
                    label-key="state" label="Select states to target" />
            <advanced-select-input
                    name="counties[]" v-model="gridForm.counties"
                    v-if="gridForm.group && gridForm.group === 'For specific counties'"
                    :close-on-select="false" :multiple="true" group-label="state" group-values="counties"
                    track-by="id" :options="groupedCounties" label-key="county"
                    label="Select counties to target" />
<!--        </template>-->

        <!--<template v-else>
            <select-input v-model="gridForm.group" label="How would you like to build posts?">
                <option v-for="option in otherCountryOptions">
                    {{ option }}
                </option>
            </select-input>
            <advanced-select-input
                    v-model="gridForm.regions"
                    @input="updateWorldCities"
                    v-if="gridForm.group && gridForm.group !== 'For all cities/regions'"
                    :close-on-select="false" :multiple="true" track-by="id" :options="regions"
                    label-key="name" label="Select regions to target" />
            <advanced-select-input
                    v-model="gridForm.cities"
                    v-if="gridForm.group && gridForm.group === 'For specific cities'"
                    :close-on-select="false" :multiple="true" track-by="id" :options="cities"
                    label-key="city" label="Select cities to target" />
        </template>-->

        <button
                type="button" @click.prevent="buildPosts" style="background:#3356ca;"
                class="bg-blue-dark text-white font-medium rounded w-full block px-8 py-4">
            {{ model ? 'Rebuild' : 'Build' }} Posts
        </button>
    </div>

    <div v-else>
        <p class="text-gray-600 text-center mt-5 mb-4">Please do not close this tab whilst we're working on adding your pages.
            <strong>Estimated time: </strong> {{ readableTime }}</p>
        <div class="relative py-3">
            <div class="flex mb-2 items-center justify-between">
                <div>
      <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-red-600 bg-red-200">
        Task in progress
      </span>
                </div>
                <div class="text-right">
      <span class="text-xs font-semibold inline-block text-red-600">
        {{ request.progress }}%
      </span>
                </div>
            </div>
            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-red-200">
                <div :style="{ width: request.progress + '%' }" class="shadow-none progress flex flex-col text-center whitespace-nowrap text-white justify-center bg-red-500"></div>
            </div>
        </div>
    </div>
</template>

<script>
    import {ExternalRepository} from '../Repositories/ExternalRepository';
    import SelectInput from './Inputs/Select.vue';
    import axios from 'axios';
    import AdvancedSelectInput from './Inputs/AdvancedSelectInput.vue';
    import TextInput from './Inputs/TextInput.vue';
    import TextareaInput from './Inputs/TextareaInput.vue';

    const moment = require( 'moment' );

    export default {
        name: 'PostBuilder',
        components: { TextareaInput, TextInput, AdvancedSelectInput, SelectInput },

        computed: {

            readableTime() {
                const seconds = this.request.estimated_time_in_seconds;

                return moment().add( seconds, 'seconds' ).fromNow( true );
            },

            stateIds() {
                return this.gridForm.hasOwnProperty( 'states' ) && this.gridForm.states ? this.gridForm.states.map( item => {
                    return item.id;
                } ) : [];
            },


            regionIds() {
                return this.gridForm.hasOwnProperty( 'regions' ) && this.gridForm.regions ? this.gridForm.regions.map( item => {
                    return item.id;
                } ) : [];
            },

            countiesIds() {
                return this.gridForm.hasOwnProperty( 'counties' ) && this.gridForm.counties ? this.gridForm.counties.map( item => {
                    return item.id;
                } ) : [];
            },

            groupedCounties() {
                let groups = {};

                if ( this.hasOwnProperty( 'counties' ) && this.counties ) {
                    for ( let county of this.counties ) {
                        if ( !Object.keys( groups ).includes( county.state ) ) {
                            groups[ county.state ] = [];
                        }

                        groups[ county.state ].push( county );
                    }

                    let _groups = [];

                    for ( let state in groups ) {
                        if ( !groups.hasOwnProperty( state ) ) {
                            continue;
                        }

                        let counties = groups[ state ];

                        _groups.push( {
                            state,
                            counties
                        } );
                    }

                    return _groups;
                }

                return [];
            }

        },

        props: {
            model: {
                type: Object | null,
                default: null
            },
            nonce: {
                type: String,
                default: null
            },
            templateId: {
                type: Number | 0,
                default: 0
            },
            ajaxUrl: {
                type: String | '',
                default: ''
            },
            initialCounties: {
                type: Array | null,
                default: null
            }
        },

        data() {
            return {
                groupOptions: ['For all cities/counties', 'For specific states', 'For specific counties'],
                otherCountryOptions: ['For all cities/regions', 'For specific regions', 'For specific cities'],
                targeting: 'all',
                gridForm: {
                    counties: [],
                    states: [],
                    regions: [],
                    post_slug: '',
                    apiKey: '',
                    uuid: '',
                    country: {
                        name: 'United States',
                        id: 236,
                    },
                    otherGroup: '',
                },
                request: {
                    started: false,
                    progress: 0,
                    estimated_time_in_seconds: 90
                },
                countries: [],
                counties: [],
                states: [],
                cities: [],
                regions: [],
                debouncers: {},
                debounces: {}
            };
        },


        beforeMount() {
            if ( this.model ) {

                this.gridForm = Object.assign( {}, this.gridForm, this.model.payload );
                this.gridForm.template = this.model.template;
                this.counties = this.initialCounties;

                const { states, counties, domains } = this;

                this.gridForm.domain = _.find( domains, { domain: this.gridForm.domain } );

                this.gridForm.states = this.gridForm.states.map( ( id ) => {
                    return _.find( states, { id } );
                } );

                this.gridForm.counties = this.gridForm.counties.map( ( id ) => {
                    return _.find( counties, { id } );
                } );

                if ( !this.gridForm.states && !this.gridForm.counties ) {
                    this.gridForm.group = 'For all cities/counties';
                }
                else if ( this.gridForm.states && !this.gridForm.counties ) {
                    this.gridForm.group = 'For specific states';
                }
                else {
                    this.gridForm.group = 'For specific counties';
                }
            }
        },

        mounted() {
            ExternalRepository.getCountries().then( ( Response ) => {
                this.countries = Response.data;
            } );

            ExternalRepository.getStates().then( ( Response ) => {
                this.states = Response.data;
            } );

            const _this = this;

            axios.get( `${this.ajaxUrl}?action=location_domination_get_settings&apiKey=1` ).then( ( { data } ) => {
                this.gridForm.apiKey = data.apiKey;
            } );

            axios.get( `${this.ajaxUrl}?action=location_domination_get_settings&uuid=1&post=${this.templateId}` ).then( ( { data } ) => {
                this.gridForm.uuid = data;
            } );

            document.body.classList.add( 'location-domination' );
        },

        methods: {
            pollWorker() {
                return ExternalRepository.pollPostRequest( this.ajaxUrl, this.templateId );
            },

            buildPosts() {
                let states = this.gridForm.states.map( item => {
                    return item.id;
                } );

                let counties = this.gridForm.counties.map( item => {
                    return item.id;
                } );

                const url = this.ajaxUrl;
                const _this = this;

                ExternalRepository.startLocalQueuePostRequest( {
                    states: states,
                    counties: counties,
                    meta_title: this.gridForm.meta_title,
                    meta_description: this.gridForm.meta_description,
                    uuid: this.gridForm.uuid,
                    apiKey: this.gridForm.apiKey
                }, url, this.templateId, this.nonce ).then( ( { data } ) => {
                    if ( data.success ) {
                        _this.request.started = true;
                        _this.pollWorker().then( ( { data } ) => {
                            console.log( { data } );
                            _this.request.progress = parseFloat( data.progress );

                            if ( _this.request.progress >= 100 ) {
                                ExternalRepository.finishLocalQueuePostRequest( url );
                            }
                        } );

                        if ( parseInt( data.batches_needed ) > 1 ) {
                            let progress = 0;

                            const POLLING_TIME_IN_SECONDS = 20;
                            const interval = setInterval( () => {
                                _this.pollWorker().then( ( { data } ) => {
                                    const _progress = parseFloat( data.progress );
                                    if ( _progress > progress ) {
                                        progress = _progress;
                                    }

                                    _this.request.progress = progress;

                                    if ( _this.request.progress >= 100 ) {
                                        clearInterval( interval );
                                        ExternalRepository.finishLocalQueuePostRequest( url );
                                    }
                                } );
                            }, (POLLING_TIME_IN_SECONDS * 1000) );
                        }
                    }
                    else {
                        console.log( data.message );
                    }
                } );
            },

            updateWorldCities() {
                let _this = this;

                if ( this.debounces.cities ) {
                    clearTimeout( this.debounces.cities );
                }

                if ( (!this.gridForm.country || this.gridForm.country.id === 236) && _this.stateIds.length === 0 ) {
                    _this.counties = [];
                }
                else {
                    this.debounces.cities = setTimeout( () => {
                        ExternalRepository.getWorldCities({
                            params: {
                                regions: this.regionIds.join( ',' ),
                            }
                        } ).then( ( { data } ) => {
                            // _this.counties = response.data;
                            _this.cities = data;

                            console.log( { cities: data, } );
                        } );
                    }, 1000 );
                }
            },

            updateRegions() {
                let _this = this;

                if ( this.debounces.regions ) {
                    clearTimeout( this.debounces.regions );
                }

                this.debounces.regions = setTimeout( () => {
                    ExternalRepository.getRegions( {
                        params: {
                            country: this.gridForm.country.id
                        }
                    } ).then( response => {
                        _this.regions = response.data;

                        console.log( { regions: _this.regions } );
                    } );
                }, 1000 );
            },

            updateCounties() {
                let _this = this;

                if ( this.debounces.counties ) {
                    clearTimeout( this.debounces.counties );
                }

                if ( _this.stateIds.length === 0 ) {
                    _this.counties = [];
                }
                else {
                    this.debounces.counties = setTimeout( () => {
                        ExternalRepository.getCounties( {
                            params: {
                                states: _this.stateIds
                            }
                        } ).then( response => {
                            _this.counties = response.data;
                        } );
                    }, 1000 );
                }
            }
        }
    };
</script>

<style scoped lang="scss">
    @import "../../scss/index.scss";

    body.location-domination {
        .multiselect__tags {
            border: 0 !important;
            padding: 8px 22px 0 0 !important;
        }

        .multiselect__placeholder {
            display: none !important;
        }

        .multiselect__tag {
            padding: 8px 27px 8px 15px !important;
            border-radius: 0px !important;
            font-weight: 400 !important;
            color: #0a1541 !important;
            border: 1px solid #dae1e7 !important;
            background: #fff !important;
            font-size: 12px !important;
        }

        .multiselect__tag-icon:focus, .multiselect__tag-icon:hover {
            background: #fff !important;
        }

        .multiselect__tag-icon {
            line-height: 30px !important;
        }

        .input {
            min-height: auto !important;
            padding: 2rem 1.25rem 0.5rem 1.25rem !important;
            border-color: #cbd5e0 !important;
            max-width: 100% !important;
        }

        .progress {
            transition: width 1000ms;
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

    table td:first-child {
        border-right: 1px solid #EBEAEF;
    }

    .custom-label input:checked + svg {
        display: block !important;
    }

    .ql-toolbar.ql-snow,
    .ql-container.ql-snow {
        border: 1px solid #dae1e7;
    }

    .border-grey {
        border-color: #EBEAEF;
    }
</style>