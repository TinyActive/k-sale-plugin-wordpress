<template>
    <div>
        <v-dialog v-model="dialog" transition="dialog-bottom-transition" fullscreen>
            <v-card>
                <div class="main-location">
                    <div class="left">
                        <div class="list-table">
                            <div class="one-table" v-for="table in listTables" >
                                <div class="table" @click="selectTable(table)" :class="table.Status == '3' ? 'ordered' : ''">
                                    <div class="table-number">
                                        Bàn {{ table.TableName }}
                                    </div>
                                    <div v-show="table.Status != '3'" class="time">Bàn trống</div>
                                    <div v-show="table.Status == '3'" class="time">{{ table.OrderDateFomated }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="right">
                        <div class="location-area">
                            <div class="location" v-for="location in listLocations"
                                :class="location.isActive ? 'active' : ''" @click="selectLocation(location)">
                                <div class="one-location">
                                    <div class="icon">
                                        <v-icon icon="mdi-map-marker"></v-icon>
                                    </div>
                                    <div class="name">{{ location.LocationName }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import { ref, reactive, computed, watch } from 'vue';
import productService from '@/services/productService';
import UtilService from '@/services/UtilsService';
import locationService from '@/services/locationService';
import tableService from '@/services/tableService';

export default {
    name: 'Location',
    components: {
    },
    props: {
        modelValue: {
            type: Boolean,
            default: false
        }
    },
    setup(props, { emit }) {
        const dialog = ref(props.modelValue);
        const utilService = new UtilService;

        watch(() => props.modelValue, newVal => {
            dialog.value = newVal;
        });

        watch(dialog, (newValue) => {
            emit('update:modelValue', newValue);
        });

        const listLocations = ref([]);
        let listTables = reactive([]);

        const handleLocation = () => {
            locationService.list().then((res) => {
                listLocations.value = res.items;
                res.items[0].isActive = true;
                loadTable(res.items[0]);
            });
        };

        // Xử lý load bàn theo location
        const loadTable = (location) => {
            tableService.getTableByLocation({ LocationID: location.LocationID }).then((res) => {
                listTables.length = 0;
                listTables.push(...Object.values(res));
            });
        };

        setInterval(() => {
            if (listTables && listTables.length) {
                listTables.forEach(item => {
                    if (item.OrderDate) {
                        const orderDate = new Date(item.OrderDate);
                        var date = new Date();
                        var utcDate = new Date(date.getTime() + date.getTimezoneOffset() * 60000);
                        // calcilating time from now to order date
                        const diff = utcDate.getTime() - orderDate.getTime();
                        // convert diff to hours, minutes, seconds
                        let hours = Math.floor(diff / (1000 * 60 * 60));
                        let minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        let seconds = Math.floor((diff % (1000 * 60)) / 1000);
                        // i want if number < 10 then add 0 before number
                        hours = hours < 10 ? `0${hours}` : hours;
                        minutes = minutes < 10 ? `0${minutes}` : minutes;
                        seconds = seconds < 10 ? `0${seconds}` : seconds;
                        item.OrderDateFomated = `${hours}:${minutes}:${seconds}`;
                    }

                });
            }
        }, 1000);

        const selectLocation = (location) => {
            listLocations.value.forEach(item => {
                item.isActive = false;
            });
            location.isActive = true;
            loadTable(location);
        };

        const selectTable = (table) => {
            const selectedLocation = listLocations.value.find(item => item.isActive);
            emit('chooseTable', {
                Table: table,
                Location: selectedLocation
            });
            dialog.value = false;
        };



        const init = async () => {
            await handleLocation();
        };

        init();

        return {
            dialog,
            listLocations,
            listTables,
            selectLocation,
            selectTable
        };
    },
};
</script>

<style scoped>
.main-location {
    display: flex;
    height: 100%;
}

.main-location .right {
    width: 300px;
    height: 100%;
    background: #3f4762;
}

.location-area {
    padding: 8px;
}

.main-location .left {
    flex: 1;
    background: #3c5477;
}

.list-table {
    display: flex;
    flex-wrap: wrap;
}

.one-table {
    width: 200px;
    height: 200px;
    padding: 16px;
}

.one-table .table {
    display: flex;
    flex-direction: column;
    background: white;
    height: 100%;
}

.table-number,
.time {
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1;
    font-weight: bold;
}


.one-location {
    display: flex;
    width: 100%;
    padding-left: 12px;
}

.location {
    height: 80px;
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    border-radius: 6px;
    font-size: 16px;
    border: 1px solid #0d7ddc;
    background: white;
}

.location.active {
    height: 80px;
    display: flex;
    align-items: center;
    background: #0d7ddc;
    margin-bottom: 8px;
    border-radius: 6px;
    font-size: 16px;
    color: white;
}

.one-location .icon {
    margin-right: 10px;
}

.one-table .table.ordered {
    background: linear-gradient(90deg, rgba(2, 0, 36, 1) 0%, rgb(155 155 255) 0%, rgba(0, 212, 255, 1) 100%);
    color: white;
}


</style>
