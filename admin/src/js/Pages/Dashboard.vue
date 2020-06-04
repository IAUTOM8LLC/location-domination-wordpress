<template>
    <div>
        <h3 class="text-dark font-bold">Location Settings</h3>
        <p class="text-grey font-medium mt-5 text-md">
            You can pull in all cities in the U.S. for all of the services you create, or you can choose to drill-down to city level by service. This option cannot be changed once you save it, so please plan accordingly.
        </p>

        <select-input v-model="locationType" class="mt-12" label="Location Type">
            <option>All Cities in the U.S.</option>
            <option>Drill Down by State, County or City</option>
        </select-input>
    </div>
</template>

<script>
    import Select from "../Components/Inputs/Select.vue";
    export default {
        name: "Dashboard",
        components: {
            "select-input": Select
        },
        data() {
            return {
                locationType: "All Cities in the U.S.",
            }
        }
    }
</script>

<style scoped>

</style>