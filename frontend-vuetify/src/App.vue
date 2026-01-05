<template>
  <v-app>
    <v-app-bar color="primary" dark flat>
      <v-app-bar-nav-icon @click="drawer = !drawer" />
      <v-toolbar-title>FIN WOOYI (Vuetify)</v-toolbar-title>
      <v-spacer />
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" app>
      <v-list density="compact" nav>
        <v-list-item to="/" title="홈" prepend-icon="mdi-home" />
        <v-list-group value="offering">
          <template #activator="{ props }">
            <v-list-item v-bind="props" title="수입" prepend-icon="mdi-cash" />
          </template>
          <v-list-item to="/offering/write" title="헌금입력" prepend-icon="mdi-note-plus" />
        </v-list-group>
      </v-list>
    </v-navigation-drawer>

    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from 'vue'
const drawer = ref(true)
</script>
