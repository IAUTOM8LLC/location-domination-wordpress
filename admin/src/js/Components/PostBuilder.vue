<template>
    <div v-if="!request.started">
        <advanced-select-input
                :preselect="preselect.country"
                v-model="gridForm.country"
                @input="updateRegions"
                :close-on-select="true" track-by="id" :options="countries"
                label-key="name" label="Select a country"/>

        <template v-if="gridForm.country && gridForm.country.id === 236">
            <select-input name="requestType" v-model="gridForm.group" label="How would you like to build posts?">
                <option v-for="option in groupOptions">
                    {{ option }}
                </option>
            </select-input>

            <advanced-select-input
                    :preselect="preselect.states"
                    name="states[]" @input="updateCounties" v-model="gridForm.states"
                    v-if="gridForm.group && gridForm.group !== 'For all cities/counties'"
                    :close-on-select="false" :multiple="true" track-by="id" :options="states"
                    label-key="state" label="Select states to target"/>
            <advanced-select-input
                    :preselect="preselect.counties"
                    @input="updateCities"
                    name="counties[]" v-model="gridForm.counties"
                    v-if="gridForm.group && (gridForm.group === 'For specific counties' || gridForm.group === 'For specific cities' )"
                    :close-on-select="false" :multiple="true" group-label="state" group-values="counties"
                    track-by="id" :options="groupedCounties" label-key="county"
                    label="Select counties to target"/>

            <advanced-select-input
                    :preselect="preselect.cities" v-model="gridForm.cities"
                    v-if="gridForm.group && gridForm.group === 'For specific cities'" :close-on-select="false"
                    :multiple="true" track-by="id" :options="cities" label-key="city" label="Select cities to target"/>

        </template>

        <template v-else>
            <select-input v-model="gridForm.group" label="How would you like to build posts?">
                <option v-for="option in otherCountryOptions">
                    {{ option }}
                </option>
            </select-input>
            <advanced-select-input
                    :preselect="preselect.regions"
                    v-model="gridForm.regions"
                    @input="updateWorldCities"
                    v-if="gridForm.group && gridForm.group !== 'For all cities/regions'"
                    :close-on-select="false" :multiple="true" track-by="id" :options="regions"
                    label-key="name" label="Select regions to target"/>
            <advanced-select-input
                    :preselect="preselect.cities"
                    v-model="gridForm.cities"
                    v-if="gridForm.group && gridForm.group === 'For specific cities'"
                    :close-on-select="false" :multiple="true" track-by="id" :options="cities"
                    label-key="city" label="Select cities to target"/>
        </template>

        <button
                type="button" @click.prevent="buildPosts" style="background:#3356ca;"
                class="bg-blue-dark text-white font-medium rounded w-full block px-8 py-4">
            {{ model ? 'Rebuild' : 'Build' }} Posts
        </button>
    </div>

    <div v-else>
        <p class="text-gray-600 text-center mt-5 mb-4">Please do not close this tab whilst we're working on adding your
            pages.
            <strong>Estimated time: </strong> {{ readableTime }}</p>
        <div class="relative py-3">
            <div class="flex mb-2 items-center justify-between">
                <div>
      <span :class="{ 'text-red-600 bg-red-200' : !request.completed, 'text-green-800 bg-green-400' : request.completed }"
            class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full">
        {{ request.completed ? 'Completed' : 'Task in progress' }}
      </span>
                </div>
                <div class="text-right">
      <span :class="{ 'text-red-600' : !request.completed, 'text-green-800' : request.completed }"
            class="text-xs font-semibold inline-block">
        {{ request.progress }}%
      </span>
                </div>
            </div>
            <div :class="{ 'bg-red-200' : !request.completed, 'bg-green-400' : request.completed }"
                 class="overflow-hidden h-2 mb-4 text-xs flex rounded">
                <div :class="{ 'bg-red-600' : !request.completed, 'bg-green-500' : request.completed }"
                     :style="{ width: request.progress + '%' }"
                     class="shadow-none progress flex flex-col text-center whitespace-nowrap text-white justify-center"></div>
            </div>
        </div>
    </div>
</template>

<script>
  import { ExternalRepository } from '../Repositories/ExternalRepository';
  import SelectInput from './Inputs/Select.vue';
  import axios from 'axios';
  import AdvancedSelectInput from './Inputs/AdvancedSelectInput.vue';
  import TextInput from './Inputs/TextInput.vue';
  import TextareaInput from './Inputs/TextareaInput.vue';

  const moment = require('moment');
  const Swal = require('sweetalert2');

  export default {
    name: 'PostBuilder',
    components: { TextareaInput, TextInput, AdvancedSelectInput, SelectInput },

    computed: {

      readableTime() {
        const seconds = this.request.estimated_time_in_seconds;

        return moment().add(seconds, 'seconds').fromNow(true);
      },

      stateIds() {
        return this.gridForm.hasOwnProperty('states') && this.gridForm.states ? this.gridForm.states.map(item => {
          return item.id;
        }) : [];
      },


      regionIds() {
        return this.gridForm.hasOwnProperty('regions') && this.gridForm.regions ? this.gridForm.regions.map(item => {
          return item.id;
        }) : [];
      },

      countiesIds() {
        return this.gridForm.hasOwnProperty('counties') && this.gridForm.counties ? this.gridForm.counties.map(item => {
          return item.id;
        }) : [];
      },

      groupedCounties() {
        let groups = {};

        if (this.hasOwnProperty('counties') && this.counties) {
          for (let county of this.counties) {
            if (!Object.keys(groups).includes(county.state)) {
              groups[county.state] = [];
            }

            groups[county.state].push(county);
          }

          let _groups = [];

          for (let state in groups) {
            if (!groups.hasOwnProperty(state)) {
              continue;
            }

            let counties = groups[state];

            _groups.push({
              state,
              counties
            });
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
      previousRequest: {
        type: Object | Array | null,
        default: null,
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
        groupOptions: ['For all cities/counties', 'For specific states', 'For specific counties', 'For specific cities'],
        otherCountryOptions: ['For all cities/regions', 'For specific regions', 'For specific cities'],
        targeting: 'all',
        gridForm: {
          counties: [],
          states: [],
          cities: [],
          regions: [],
          post_slug: '',
          apiKey: '',
          uuid: '',
          country: {
            name: 'United States',
            id: 236
          },
          otherGroup: ''
        },
        request: {
          started: false,
          completed: false,
          progress: 0,
          estimated_time_in_seconds: 140
        },
        preselect: {
          country: false,
          cities: false,
          states: false,
          regions: false,
          counties: false,
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

      if (this.previousRequest) {
        if (this.previousRequest && this.previousRequest.hasOwnProperty('group')) {
          this.gridForm = Object.assign({}, this.gridForm, { group: this.previousRequest.group });
        }

        //                if (this.previousRequest && this.previousRequest.hasOwnProperty( 'states' ) ) {
        //                    this.preselect = Object.assign( {}, this.preselect, { states: this.previousRequest.states } );
        //                }
        //
        //                if (this.previousRequest && this.previousRequest.hasOwnProperty( 'counties' ) ) {
        //                    this.preselect = Object.assign( {}, this.preselect, { states: this.previousRequest.counties } );
        //                }
        //
        //                if (this.previousRequest && this.previousRequest.hasOwnProperty( 'cities' ) ) {
        //                    this.preselect = Object.assign( {}, this.preselect, { states: this.previousRequest.cities } );
        //                }
      }
    },

    mounted() {
      const _this = this;

      ExternalRepository.getCountries().then((Response) => {
        this.countries = Response.data;

        const _countries = this.countries;

        if (this.previousRequest && this.previousRequest.hasOwnProperty('country')) {
          const match = _countries.filter(function (state) {
            return parseInt(_this.previousRequest.country) === state.id;
          });

          if (match[0]) {
            this.preselect.country = match[0];
          }
        }
      });

      ExternalRepository.getStates().then((Response) => {
        this.states = Response.data;

        const _states = this.states;

        if (this.previousRequest && this.previousRequest.hasOwnProperty('states')) {
          this.preselect.states = this.previousRequest.states.map((id) => {
            const match = _states.filter(function (state) {
              return parseInt(id) === state.id;
            });

            return match[0];
          }).filter(state => state);
        }
      });

      axios.get(`${this.ajaxUrl}?action=location_domination_get_settings&apiKey=1`).then(({ data }) => {
        this.gridForm.apiKey = data.apiKey;
      });

      axios.get(`${this.ajaxUrl}?action=location_domination_get_settings&uuid=1&post=${this.templateId}`).then(({ data }) => {
        this.gridForm.uuid = data;
      });

      document.body.classList.add('location-domination');
    },

    methods: {
      pollWorker() {
        return ExternalRepository.pollPostRequest(this.ajaxUrl, this.templateId);
      },

      buildPosts() {
        let cities = this.gridForm.cities.map(item => {
          return item.id;
        });

        const form = {
          cities,
          country: this.gridForm.country.id,
          meta_title: this.gridForm.meta_title,
          meta_description: this.gridForm.meta_description,
          uuid: this.gridForm.uuid,
          apiKey: this.gridForm.apiKey,
          group: this.gridForm.group,
        };

        if (form.country === 236) {
          let states = this.gridForm.states.map(item => {
            return item.id;
          });

          let counties = this.gridForm.counties.map(item => {
            return item.id;
          });

          form.states = states;
          form.counties = counties;
        } else {
          form.regions = this.gridForm.regions.map(item => {
            return item.id;
          });
        }

        const url = this.ajaxUrl;

        ExternalRepository.previewPostRequest(form, url, this.templateId, this.nonce).then(({ data }) => {
          const postCount = data.success ? data.posts : 0;

          Swal.fire({
            title: 'Are you sure?',
            html: `This will overwrite any existing pages within this template so make sure you include all locations. <strong>${postCount} pages</strong> will be created if you continue.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, resume!',
            cancelButtonText: 'No, I\'m not ready'
          }).then((result) => {
            if (result.value) {
              const _this = this;

              ExternalRepository.startLocalQueuePostRequest(form, url, this.templateId, this.nonce).then(({ data }) => {
                if (data.success) {
                  _this.request.started = true;
                  _this.pollWorker().then(({ data }) => {
                    console.log({ data });
                    _this.request.estimated_time_in_seconds = 60;
                    _this.request.progress = parseFloat(data.progress);

                    if (_this.request.progress >= 100) {
                      _this.request.completed = true;
                      ExternalRepository.finishLocalQueuePostRequest(url);
                    }
                  });

                  if (parseInt(data.batches_needed) > 1) {
                    let progress = 0;

                    const POLLING_TIME_IN_SECONDS = 20;
                    const interval = setInterval(() => {
                      _this.pollWorker().then(({ data }) => {
                        const _progress = parseFloat(data.progress);
                        const estimated_time_remaining = parseFloat(data.estimated_time_remaining);

                        if (_progress > progress) {
                          progress = _progress;
                        }

                        if (estimated_time_remaining) {
                          _this.request.estimated_time_in_seconds = estimated_time_remaining;
                        }

                        _this.request.progress = progress;

                        if (_this.request.progress >= 100) {
                          _this.request.completed = true;
                          clearInterval(interval);
                          ExternalRepository.finishLocalQueuePostRequest(url);
                        }
                      });
                    }, (POLLING_TIME_IN_SECONDS * 1000));
                  }
                } else {
                  console.log(data.message);
                  Swal.fire(
                      'Oops, something went wrong.',
                      'We couldn\'t process your post request at this time. Please make sure that your account is connected to Location Domination. Once you have done so, please hit "Send to Location Domination" at the top of this page.',
                      'error'
                  );
                }
              });

              Swal.fire(
                  'Awesome!',
                  'We\'ve started to process your post request.',
                  'success'
              );
              // For more information about handling dismissals please visit
              // https://sweetalert2.github.io/#handling-dismissals
            } else if (result.dismiss === Swal.DismissReason.cancel) {
              Swal.fire(
                  'Cancelled',
                  'We\'ve cancelled your post request.',
                  'error'
              );
            }
          });
        });
      },

      updateWorldCities() {
        let _this = this;

        if (this.debounces.cities) {
          clearTimeout(this.debounces.cities);
        }

        if ((!this.gridForm.country || this.gridForm.country.id === 236) && _this.stateIds.length === 0) {
          _this.counties = [];
        } else {
          this.debounces.cities = setTimeout(() => {
            ExternalRepository.getWorldCities({
              params: {
                regions: this.regionIds.join(',')
              }
            }).then(({ data }) => {
              // _this.counties = response.data;
              _this.cities = data;

              const _cities = this.cities;

              if (this.previousRequest && this.previousRequest.hasOwnProperty('cities')) {
                this.preselect.cities = this.previousRequest.cities.map((id) => {
                  const match = _cities.filter(function (city) {
                    return parseInt(id) === city.id;
                  });

                  return match[0];
                }).filter(city => city);
              }
            });
          }, 1000);
        }
      },

      updateCities() {
        let _this = this;

        if (this.debounces.cities) {
          clearTimeout(this.debounces.cities);
        }

        if (_this.stateIds.length === 0) {
          _this.counties = [];
        } else {
          this.debounces.counties = setTimeout(() => {
            ExternalRepository.getCities({
              params: {
                filter: this.countiesIds.join(',')
              }
            }).then(({ data }) => {
              _this.cities = data;

              const _cities = _this.cities;

              if (_this.previousRequest.hasOwnProperty('cities') && _this.preselect.cities === false) {
                _this.preselect.cities = _this.previousRequest.cities.map((id) => {
                  const match = _cities.filter(function (city) {
                    return parseInt(id) === city.id;
                  });

                  return match[0];
                }).filter(city => city);
              }
            });
          }, 1000);
        }
      },

      updateRegions() {
        let _this = this;

        this.gridForm = Object.assign({}, this.gridForm, {
          cities: [],
          regions: [],
          states: [],
          counties: [],
        });

        if (this.debounces.regions) {
          clearTimeout(this.debounces.regions);
        }

        this.debounces.regions = setTimeout(() => {
          ExternalRepository.getRegions({
            params: {
              country: this.gridForm.country.id
            }
          }).then(response => {
            _this.regions = response.data;

            const _regions = this.regions;

            if (this.previousRequest && this.previousRequest.hasOwnProperty('regions')) {
              this.preselect.regions = this.previousRequest.regions.map((id) => {
                const match = _regions.filter(function (region) {
                  return parseInt(id) === region.id;
                });

                return match[0];
              }).filter(region => region);
            }
          });
        }, 1000);
      },

      updateCounties() {
        let _this = this;

        if (this.debounces.counties) {
          clearTimeout(this.debounces.counties);
        }

        if (_this.stateIds.length === 0) {
          _this.counties = [];
        } else {
          this.debounces.counties = setTimeout(() => {
            ExternalRepository.getCounties({
              params: {
                states: _this.stateIds
              }
            }).then(response => {
              _this.counties = response.data;

              const _counties = _this.counties;

              if (_this.previousRequest.hasOwnProperty('counties') && _this.preselect.counties === false) {
                _this.preselect.counties = _this.previousRequest.counties.map((id) => {
                  const match = _counties.filter(function (county) {
                    return parseInt(id) === county.id;
                  });

                  return match[0];
                }).filter(county => county);
              }
            });
          }, 1000);
        }
      }
    }
  };
</script>

<style scoped lang="scss">
    @import "../../scss/index.scss";
    @import '~@sweetalert2/theme-borderless/borderless.scss';

    body.location-domination {
        .multiselect__tags {
            border: 0 !important;
            padding: 8px 22px 0 0 !important;
        }

        .swal2-container.swal2-backdrop-show, .swal2-container.swal2-noanimation {
            z-index: 999999 !important;
        }

        .swal2-modal {
            h2 {
                color: #fff !important;
            }
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