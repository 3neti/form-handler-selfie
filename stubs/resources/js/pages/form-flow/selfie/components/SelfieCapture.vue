<script setup lang="ts">
import FormFlowNotice from "@/pages/form-flow/core/components/FormFlowNotice.vue";
import { ref, computed, onMounted } from "vue";
import { useCamera } from "../composables/useCamera";
import { Button } from "@/components/ui/button";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { AlertCircle, Camera, RotateCw } from "lucide-vue-next";
import CameraPermissionAlert from "./CameraPermissionAlert.vue";
import { useFormFlowPresentation } from "@/pages/form-flow/core/components/formFlowPresentation";
import FormFlowAction from "@/pages/form-flow/core/components/FormFlowAction.vue";
import FormFlowActions from "@/pages/form-flow/core/components/FormFlowActions.vue";
import FormFlowScreen from "@/pages/form-flow/core/components/FormFlowScreen.vue";
import type { FormFlowUiVariant } from "@/pages/form-flow/core/components/formFlowUiVariant";

export interface SelfieConfig {
  width?: number;
  height?: number;
  quality?: number;
  format?: string;
  facing_mode?: "user" | "environment";
  show_guide?: boolean;
}

export interface SelfieData {
  selfie: string;
  image?: string;
  width: number;
  height: number;
  format: string;
}

interface PackageVersion {
  name: string;
  version: string;
}

interface Props {
  config?: SelfieConfig;
  uiVariant?: FormFlowUiVariant | string | null;
  actionPlacement?:
    | "inline"
    | "bottom"
    | "bottom_sticky"
    | "viewport_bottom"
    | string
    | null;
  uiLayout?: Record<string, unknown> | null;
  appName?: string | null;
  appLogo?: string | null;
  packageVersions?: PackageVersion[] | Record<string, string> | null;
  showPackageVersions?: boolean;
  previewMode?: boolean;
}

interface Emits {
  (e: "submit", value: SelfieData): void;
  (e: "cancel"): void;
}

const props = withDefaults(defineProps<Props>(), {
  config: () => ({}),
  uiVariant: "default",
  actionPlacement: null,
  uiLayout: null,
  appName: null,
  appLogo: null,
  packageVersions: null,
  showPackageVersions: false,
  previewMode: false,
});

const emit = defineEmits<Emits>();

const videoRef = ref<HTMLVideoElement | null>(null);
const canvasRef = ref<HTMLCanvasElement | null>(null);
const cameraAlertRef = ref<InstanceType<typeof CameraPermissionAlert> | null>(
  null,
);

const hasCaptured = ref(false);
const capturedImage = ref("");

const { stream, error: cameraError, startCamera, stopCamera } = useCamera();

const width = computed(() => props.config?.width ?? 640);
const height = computed(() => props.config?.height ?? 480);
const quality = computed(() => props.config?.quality ?? 0.85);
const format = computed(() => props.config?.format ?? "image/jpeg");
const facingMode = computed(() => props.config?.facing_mode ?? "user");
const showGuide = computed(() => props.config?.show_guide ?? true);
const presentation = useFormFlowPresentation();
const isImmersive = computed(
  () => (presentation.value?.layout.density ?? props.uiVariant) === "immersive",
);
const isViewportBottom = computed(
  () =>
    (presentation.value?.actions.placement ?? props.actionPlacement) ===
    "viewport_bottom",
);
const formClass = computed(() =>
  isImmersive.value ? "flex flex-1 flex-col gap-4" : "space-y-6",
);
const mediaClass = computed(() =>
  isImmersive.value
    ? "h-[56vh] min-h-80 w-full object-cover"
    : "w-full h-96 object-cover",
);

async function initCamera() {
  if (props.previewMode) return;
  try {
    const mediaStream = await startCamera({
      video: {
        facingMode: facingMode.value,
        width: width.value,
        height: height.value,
      },
    });

    if (videoRef.value) {
      videoRef.value.srcObject = mediaStream;
    }
  } catch (err: any) {
    if (err.name === "NotAllowedError") {
      cameraAlertRef.value?.open();
    }
  }
}

function captureSelfie() {
  if (props.previewMode) return;
  if (!videoRef.value || !canvasRef.value) return;

  const canvas = canvasRef.value;
  const video = videoRef.value;

  // Set canvas size to match video
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;

  // Draw video frame to canvas
  const ctx = canvas.getContext("2d");
  if (ctx) {
    ctx.drawImage(video, 0, 0);

    // Convert to base64 with configured quality
    capturedImage.value = canvas.toDataURL(format.value, quality.value);
    hasCaptured.value = true;

    // Stop camera after capture
    stopCamera();
  }
}

function retakeSelfie() {
  hasCaptured.value = false;
  capturedImage.value = "";
  initCamera();
}

function handleSubmit() {
  if (props.previewMode) return;
  if (!capturedImage.value) return;

  const selfieData: SelfieData = {
    selfie: capturedImage.value,
    image: capturedImage.value,
    width: canvasRef.value?.width ?? width.value,
    height: canvasRef.value?.height ?? height.value,
    format: format.value,
  };

  emit("submit", selfieData);
}

function handleCancel() {
  emit("cancel");
}

onMounted(() => {
  if (props.previewMode) return;
  initCamera();
});
</script>

<template>
  <FormFlowScreen
    title="Selfie Required"
    description="Please take a clear photo of yourself"
    :variant="uiVariant"
    :app-name="appName"
    :app-logo="appLogo"
    :package-versions="packageVersions"
    :show-package-versions="showPackageVersions"
    version-context="selfie"
  >
    <template #icon>
      <Camera class="h-5 w-5" />
    </template>

    <template #alert>
      <FormFlowNotice v-if="cameraError" class="mb-6" role="error">
        <AlertDescription>
          {{ cameraError }}
        </AlertDescription>
      </FormFlowNotice>
    </template>

    <form @submit.prevent="handleSubmit" :class="formClass">
      <!-- Camera/Preview Area -->
      <div class="flex min-h-0 flex-1 flex-col gap-4">
        <!-- Live Camera Feed (before capture) -->
        <div
          v-if="!hasCaptured"
          data-flow-capture-surface
          class="relative rounded-lg border overflow-hidden bg-black"
        >
          <video
            ref="videoRef"
            autoplay
            playsinline
            :class="mediaClass"
          ></video>
          <div v-if="showGuide" class="absolute inset-0 pointer-events-none">
            <!-- Face guide overlay -->
            <div class="absolute inset-0 flex items-center justify-center">
              <div
                class="selfie-face-guide border-4 border-white/50 rounded-full"
              ></div>
            </div>
          </div>
        </div>

        <!-- Captured Image Preview -->
        <div
          v-else
          data-flow-capture-surface
          class="relative rounded-lg border overflow-hidden"
        >
          <img :src="capturedImage" alt="Your selfie" :class="mediaClass" />
        </div>

        <!-- Camera Instructions -->
        <div
          v-if="!hasCaptured"
          class="text-sm text-muted-foreground text-center"
        >
          <p>Position your face in the oval guide</p>
          <p class="text-xs mt-1">
            Make sure your face is well-lit and clearly visible
          </p>
        </div>
      </div>

      <FormFlowActions
        primary-role="capture"
        secondary-role="cancel"
        v-if="!hasCaptured"
        :variant="uiVariant"
        primary-label="Capture Photo"
        primary-type="button"
        :action-placement="actionPlacement"
        :primary-disabled="!!cameraError"
        @primary="captureSelfie"
        @secondary="handleCancel"
      >
      </FormFlowActions>

      <div
        v-if="hasCaptured && isViewportBottom"
        class="h-24 shrink-0"
        aria-hidden="true"
      />

      <div
        v-if="hasCaptured"
        class="grid gap-3 pt-4 sm:grid-cols-3"
        :class="{
          'fixed inset-x-0 z-30 mx-auto w-full max-w-md grid-cols-3 gap-2 px-5 pt-0 sm:max-w-lg':
            (presentation?.actions.placement ?? actionPlacement) ===
            'viewport_bottom',
          'sticky bottom-0 z-10 -mx-4 mt-auto border-t bg-card/95 px-4 py-3 backdrop-blur supports-[backdrop-filter]:bg-card/85 sm:-mx-5 sm:px-5':
            (presentation?.actions.placement ?? actionPlacement) ===
            'bottom_sticky',
          'mt-auto border-t':
            (presentation?.actions.placement ?? actionPlacement) === 'bottom' ||
            (!actionPlacement && isImmersive),
        }"
        :style="
          isViewportBottom
            ? { bottom: 'max(0.2in, calc(env(safe-area-inset-bottom) + 1rem))' }
            : undefined
        "
      >
        <FormFlowAction
          type="button"
          variant="outline"
          class="w-full rounded-full"
          @click="handleCancel"
          role="cancel"
          label="Back"
        />

        <!-- Capture Button (before capture) -->
        <FormFlowAction
          type="button"
          variant="outline"
          class="w-full rounded-full"
          @click="retakeSelfie"
          role="retake"
          label="Retake"
        />
        <FormFlowAction
          type="submit"
          role="continue"
          label="Continue"
          class="w-full rounded-full"
        />
      </div>
    </form>

    <!-- Hidden canvas for image capture -->
    <canvas ref="canvasRef" class="hidden"></canvas>

    <!-- Camera Permission Alert Modal -->
    <CameraPermissionAlert ref="cameraAlertRef" />
  </FormFlowScreen>
</template>

<style scoped>
.selfie-face-guide {
  width: min(16rem, 58vw);
  height: min(20rem, 46vh);
  min-height: 14rem;
}
</style>
