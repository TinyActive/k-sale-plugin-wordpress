<template>
    <div class="seat-setting">
        <div class="header-page">
            <div class="title">Thiết lập bàn</div>
            <div class="action">
                <v-btn variant="flat" color="info" @click="isShowPopup = true">Thêm khu vực</v-btn>
            </div>

        </div>
        <div class="setting-location">
            <v-data-table :hide-default-footer="false" :headers="headers" :items="items" item-value="name">
                <template v-slot:item.LocationName="{ item }">
                    <div class="text-link" @click="openUpdate(item.LocationID)">{{ item.LocationName }}</div>
                </template>
                <!-- <template v-slot:item.NumberOfTable="{ item }">
                    <div>{{ item.NumberOfTable }}</div>
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-icon @click="isShowPopup = true">mdi-pencil</v-icon>
                    <v-icon @click="isShowPopup = true">mdi-delete</v-icon>
                </template> -->
            </v-data-table>
        </div>
        <TablePopup :dataUpdate="itemUpdate" @reload="handleInitData" v-if="isShowPopup" v-model="isShowPopup" />
    </div>
</template>

<script>
import { ref } from 'vue';
import TablePopup from '../components/TablePopup.vue';
import locationService from '../services/locationService';

export default {
    name: 'SeatSetting',
    components: {
        TablePopup,
    },
    setup() {
        const isShowPopup = ref(false);
        let headers = [
            {
                title: 'Tên khu vực',
                align: 'start',
                key: 'LocationName',
            },
            {
                title: 'Số lượng bàn',
                align: 'center',
                key: 'NumberOfTable',
            },
        ]

        let items = ref([]);
        let itemUpdate = ref(null);

        const handleInitData = () => {
            itemUpdate.value = null;
            locationService.list().then(data => {
                items.value = data.items;
            });
        }


        const openUpdate = (locationID) => {
            itemUpdate.value = items.value.find(x => x.LocationID === locationID);
            isShowPopup.value = true;
        }

        const init = () => {
            handleInitData();
        }

        init();

        return { headers, items, isShowPopup, handleInitData, openUpdate, itemUpdate };
    }
}
</script>

<style scoped>
.title {
    font-size: 24px;
    font-weight: 500;
    align-items: center;
    display: flex;
}

.header-page {
    display: flex;
    justify-content: space-between;
    margin-top: 22px;
    margin-bottom: 22px;
}

.seat-setting {
    max-width: 900px;
    margin: 0 auto;
}
</style>