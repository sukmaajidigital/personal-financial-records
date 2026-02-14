<script lang="ts" setup>
import type { ToasterProps } from "vue-sonner"
import { CircleCheckIcon, InfoIcon, Loader2Icon, OctagonXIcon, TriangleAlertIcon, XIcon } from "lucide-vue-next"
import { Toaster as Sonner } from "vue-sonner"
import { cn } from "@/lib/utils"

const props = defineProps<ToasterProps>()
</script>

<template>
  <Sonner
    :class="cn('toaster group', props.class)"
    :style="{
      '--normal-bg': 'var(--popover)',
      '--normal-text': 'var(--popover-foreground)',
      '--normal-border': 'var(--border)',
      '--border-radius': 'var(--radius)',
    }"
    :toast-options="{
      classNames: {
        toast: '!shadow-lg',
        success: '!bg-green-50 !text-green-800 !border-green-200 dark:!bg-green-950 dark:!text-green-200 dark:!border-green-800',
        error: '!bg-red-50 !text-red-800 !border-red-200 dark:!bg-red-950 dark:!text-red-200 dark:!border-red-800',
        warning: '!bg-yellow-50 !text-yellow-800 !border-yellow-200 dark:!bg-yellow-950 dark:!text-yellow-200 dark:!border-yellow-800',
        info: '!bg-blue-50 !text-blue-800 !border-blue-200 dark:!bg-blue-950 dark:!text-blue-200 dark:!border-blue-800',
      },
    }"
    v-bind="props"
  >
    <template #success-icon>
      <CircleCheckIcon class="size-4 !text-green-600 dark:!text-green-400" />
    </template>
    <template #info-icon>
      <InfoIcon class="size-4 !text-blue-600 dark:!text-blue-400" />
    </template>
    <template #warning-icon>
      <TriangleAlertIcon class="size-4 !text-yellow-600 dark:!text-yellow-400" />
    </template>
    <template #error-icon>
      <OctagonXIcon class="size-4 !text-red-600 dark:!text-red-400" />
    </template>
    <template #loading-icon>
      <div>
        <Loader2Icon class="size-4 animate-spin" />
      </div>
    </template>
    <template #close-icon>
      <XIcon class="size-4" />
    </template>
  </Sonner>
</template>
