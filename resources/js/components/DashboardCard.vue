<template>
    <div class="bg-white shadow-md p-4 rounded-lg flex items-center gap-4">
      <component :is="icon" class="w-8 h-8 text-gray-600" />
      <div>
        <h5 class="text-lg font-semibold">{{ title }}</h5>
        <p class="text-xl font-bold">{{ value }}</p>
      </div>
    </div>
  </template>

<script>
export default {
  props: {
    title: String,
    value: [String, Number],
    icon: [String, Object, Function],
    iconColor: {
      type: String,
      default: 'text-gray-600'
    }
  }
};
</script>
