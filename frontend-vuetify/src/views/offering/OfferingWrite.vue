<template>
  <div class="pa-4">
    <v-container>
      <v-row>
        <v-col cols="12" md="4">
          <v-card variant="outlined">
            <v-card-title>헌금 입력</v-card-title>
            <v-card-text>
              <v-text-field
                v-model="date"
                type="date"
                label="등록일"
                density="comfortable"
              />
              <v-select
                v-model="parent"
                :items="parents"
                item-title="TITLE"
                item-value="NO"
                label="대분류"
                :loading="loadingParent"
                density="comfortable"
                @update:modelValue="onParentChange"
              />
              <v-select
                v-model="child"
                :items="children"
                item-title="TITLE"
                item-value="NO"
                label="소분류"
                :loading="loadingChild"
                density="comfortable"
              />
              <v-text-field
                v-model="name"
                label="헌금자"
                density="comfortable"
              />
              <v-text-field
                v-model="price"
                label="금액"
                type="number"
                density="comfortable"
              />
              <v-text-field
                v-model="etc"
                label="추가입력"
                density="comfortable"
              />
              <v-btn
                color="primary"
                class="mt-2"
                block
                @click="register"
                :loading="submitting"
              >
                등록
              </v-btn>
              <v-alert
                v-if="error"
                type="error"
                class="mt-3"
                density="compact"
              >
                {{ error }}
              </v-alert>
              <v-alert
                v-if="success"
                type="success"
                class="mt-3"
                density="compact"
              >
                {{ success }}
              </v-alert>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="8">
          <v-card variant="outlined">
            <v-card-title>대/소분류 상태</v-card-title>
            <v-card-text>
              <v-chip class="ma-1" v-for="p in parents" :key="p.NO" color="primary" variant="outlined">
                {{ p.TITLE }}
              </v-chip>
              <div class="mt-3">
                <v-chip class="ma-1" v-for="c in children" :key="c.NO" color="secondary" variant="outlined">
                  {{ c.TITLE }}
                </v-chip>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'

const parents = ref([])
const children = ref([])
const loadingParent = ref(false)
const loadingChild = ref(false)

const date = ref(new Date().toISOString().slice(0, 10))
const parent = ref(null)
const child = ref(null)
const name = ref('')
const price = ref('')
const etc = ref('')
const submitting = ref(false)
const error = ref('')
const success = ref('')

const yearFromDate = () => {
  if (!date.value) return new Date().getFullYear()
  return Number(date.value.slice(0, 4))
}

const fetchParents = async () => {
  loadingParent.value = true
  error.value = ''
  try {
    const res = await api.post('/offering/offering_list', {
      'is-income': 'Y',
      parent: 0,
      year: yearFromDate()
    })
    if (res.data?.status) {
      parents.value = res.data.data
      parent.value = res.data.data?.[0]?.NO ?? null
      if (parent.value) {
        await fetchChildren()
      } else {
        children.value = []
        child.value = null
      }
    } else {
      error.value = '대분류를 불러오지 못했습니다.'
    }
  } catch (e) {
    error.value = e?.message || '대분류 조회 오류'
  } finally {
    loadingParent.value = false
  }
}

const fetchChildren = async () => {
  if (!parent.value) {
    children.value = []
    child.value = null
    return
  }
  loadingChild.value = true
  error.value = ''
  try {
    const res = await api.post('/offering/offering_list', {
      'is-income': 'Y',
      parent: parent.value,
      year: yearFromDate()
    })
    if (res.data?.status) {
      children.value = res.data.data
      child.value = res.data.data?.[0]?.NO ?? null
    } else {
      children.value = []
      child.value = null
      error.value = '소분류를 불러오지 못했습니다.'
    }
  } catch (e) {
    error.value = e?.message || '소분류 조회 오류'
  } finally {
    loadingChild.value = false
  }
}

const onParentChange = async () => {
  await fetchChildren()
}

const register = async () => {
  if (!parent.value || !child.value) {
    error.value = '대분류/소분류를 선택하세요.'
    return
  }
  if (!name.value || !price.value) {
    error.value = '헌금자와 금액을 입력하세요.'
    return
  }
  submitting.value = true
  error.value = ''
  success.value = ''
  try {
    const res = await api.post('/offering/offering_register', {
      type: child.value,
      regDate: date.value,
      etc: etc.value || '',
      is_online: 'N',
      name: name.value,
      price: Number(price.value)
    })
    if (res.data?.status) {
      success.value = '등록되었습니다.'
    } else {
      error.value = '등록 실패'
    }
  } catch (e) {
    error.value = e?.message || '등록 오류'
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  fetchParents()
})
</script>
