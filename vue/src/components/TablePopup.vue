<template>
    <v-dialog max-width="700" v-model="isActive" :persistent="false">
        <template v-slot:default="{ isActive }">
            <v-card title="Thêm khu vực">
                <form>
                    <v-card-text>
                        <div class="header">
                            <v-text-field v-model="titleLocation.value.value"
                                :error-messages="titleLocation.errorMessage.value" autofocus label="Tên khu vực"
                                variant="outlined"></v-text-field>

                        </div>
                        <v-divider></v-divider>
                        <div class="list-seat">
                            <div v-for="(seat, index) in numberOfSeat" class="table-icon-wrap">
                                <div class="table-icon">{{ index + 1 }}</div>
                            </div>
                            <div class="table-icon-wrap-add" @click="addTable">
                                <v-icon color="info" icon="mdi-plus"></v-icon>
                            </div>
                        </div>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" text="Lưu" color="info" @click="saveLocation"></v-btn>
                    </v-card-actions>
                </form>
            </v-card>
        </template>
    </v-dialog>
</template>

<script>
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { useField, useForm } from 'vee-validate';
import localtionService from '../services/locationService';

export default {
    name: 'TablePopup',
    components: {
    },
    props: {
        modelValue: {
            type: Boolean,
            required: true
        },
        dataUpdate: {
            type: Object,
        }
    },
    setup(props, { emit }) {

        const { handleSubmit, handleReset } = useForm({
            validationSchema: {
                titleLocation(value) {
                    if (value?.length > 0) return true
                    return 'Tên khu vực ít nhất một ký tự.'
                },
            },
        })

        const numberOfSeat = ref(0);
        const titleLocation = useField("titleLocation");
        const isActive = ref(props.modelValue);

        watch(() => props.modelValue, (newValue) => {
            isActive.value = newValue;
        });

        watch(isActive, (newValue) => {
            if(!newValue){
                emit('reload');
            }
            emit('update:modelValue', newValue);
        });

        // Thực hiện lưu dữ liệu khu vực
        const saveLocation = handleSubmit(async ({ titleLocation }) => {
            const LocationID = props.dataUpdate ? props.dataUpdate.LocationID : null;
            const data = await localtionService.saveLocation({ LocationID: LocationID, LocationName: titleLocation, NumberOfSeat: numberOfSeat.value });
            if (data) {
                
                isActive.value = false;
            }
        });

        const addTable = () => {
            numberOfSeat.value++;
        };

        const handleMode = () => {
            if (props.dataUpdate) {
                titleLocation.value.value = props.dataUpdate.LocationName;
                numberOfSeat.value = Number(props.dataUpdate.NumberOfTable);
            }
        }

        const init = () => {
            handleMode();
        }

        init();

        return {
            isActive,
            numberOfSeat,
            saveLocation,
            addTable,
            titleLocation
        };
    },
};
</script>

<style scoped>
.popup {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 999;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    font-size: 24px;
    font-weight: 500;
    padding: 20px;
    text-align: center;
    max-width: 980px;
}

.table-icon {
    position: relative;
    width: 70px;
    height: 70px;
    background-color: #c4cdd5;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table-icon:before,
.table-icon:after {
    content: "";
    position: absolute;
    width: 23px;
    height: 9px;
    background-color: #c4cdd5;
    border-radius: 50px 50px 0 0;
    top: 30px;
}

.table-icon:before {
    left: -19px;
    transform: rotate(-90deg);
}

.table-icon:after {
    right: -19px;
    transform: rotate(90deg);
}

.list-seat {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(5, 1fr);
}

.table-icon-wrap {
    position: relative;
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;

    &:hover {
        border: 1px solid #c4cdd5;
    }
}

.table-icon-wrap-add {
    position: relative;
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 1px solid #c4cdd5;
}
</style>