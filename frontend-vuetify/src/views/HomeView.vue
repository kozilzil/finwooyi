<template>
  <div class="pa-6">
    <v-container>
      <v-row>
        <v-col cols="12" md="8">
          <v-card class="mb-4" variant="outlined">
            <v-card-title>Vue + Vuetify 프런트 시작</v-card-title>
            <v-card-text>
              <p>이 인스턴스는 CodeIgniter 백엔드와 분리된 새 프런트 엔드입니다.</p>
              <p>아래 버튼으로 API 베이스를 확인하고, 헌금/지출 등 페이지를 단계적으로 옮길 수 있습니다.</p>
            </v-card-text>
            <v-card-actions>
              <v-btn color="primary" @click="checkApi">API 연결 테스트</v-btn>
              <v-spacer />
              <v-chip label color="info" variant="outlined">API: {{ apiBase }}</v-chip>
            </v-card-actions>
          </v-card>
          <v-alert type="info" v-if="pingResult" class="mb-4">
            {{ pingResult }}
          </v-alert>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const apiBase = import.meta.env.VITE_API_BASE || 'http://127.0.0.1:5000'
const pingResult = ref('')

const checkApi = async () => {
  try {
    const res = await axios.get(apiBase, { withCredentials: true })
    pingResult.value = `응답 OK (status ${res.status})`
  } catch (e) {
    pingResult.value = `요청 실패: ${e?.message || e}`
  }
}
</script>
