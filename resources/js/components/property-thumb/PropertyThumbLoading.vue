<script setup lang="ts">

</script>

<template>
  <div @click="$emit('click')" class="h-[400px] animate-pulse rounded-lg bg-input/40 m-2">
    <div class="img relative h-[200px] p-2 bg-input rounded-t-lg bg-cover bg-center">
      <div class="property-status absolute top-2 left-2 w-[60px] bg-gray-800/40 rounded-full">&nbsp;&nbsp;</div>
      <div class="action-buttons absolute top-2 right-2 flex gap-2">
        <div class="flex-1 p-1 rounded-lg bg-gray-800/40 w-[35px]">&nbsp;&nbsp;</div>
        <div class="flex-1 p-1 rounded-lg bg-gray-800/40 w-[35px]">&nbsp;&nbsp;</div>
      </div>
      <div class="absolute bottom-2 left-2 flex flex-col items-end gap-2 text-sm">
        <div class="flex-1 p-1 rounded-lg bg-gray-800/40 w-[35px]">&nbsp;&nbsp;</div>
      </div>
      <div class="absolute bottom-2 right-2 flex flex-col items-end gap-2 text-sm">
        <div class="flex-1 p-1 rounded-lg bg-gray-800/40 w-[50px]">&nbsp;&nbsp;</div>
        <div class="flex-1 p-1 rounded-lg bg-gray-800/40 w-[100px]">&nbsp;&nbsp;</div>
      </div>
    </div>
    <div class="bottom-container">
      <div class="price-container flex py-2 gap-2 px-2">
        <div class="font-semibold text-lg/4 py-2.5 my-auto rounded-lg bg-gray-600/40 w-[100px]">
          &nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        <div class="text-xs text-muted-foreground mt-auto truncate overflow-hidden whitespace-nowrap bg-gray-600/40 w-[150px] rounded-lg">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
      </div>
      <div class="address-container px-2 truncate overflow-hidden whitespace-nowrap ">
        <div class=" bg-gray-600/40 w-full rounded-full">&nbsp;&nbsp;&nbsp;&nbsp;</div>
      </div>
      <div class="flex flex-row py-2 gap-2 px-2 ">
        <div class="bg-gray-600/40 w-[150px] rounded-lg">&nbsp;</div>
      </div>
      <div class="grid grid-cols-3 justify-around bg-gray-300 dark:bg-gray-700 py-1">
        &nbsp;&nbsp;
      </div>
      <div class="grid grid-cols-3 gap-2 mt-1 px-2">
        <div class="bg-gray-600/40 w-full h-[40px] rounded-lg">&nbsp;</div>
        <div class="bg-gray-600/40 w-full h-[40px] rounded-lg">&nbsp;</div>
        <div class="bg-gray-600/40 w-full h-[40px] rounded-lg">&nbsp;</div>
      </div>
      <div class="p-1 bg-transparent"></div>
    </div>
    <slot/>
  </div>
</template>

<style scoped>

</style>
