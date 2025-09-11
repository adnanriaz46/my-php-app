<script setup lang="ts">
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogDescription
} from "@/components/ui/dialog";
import {Button} from "@/components/ui/button";
import {computed, ref} from "vue";
import {VisuallyHidden} from "reka-ui";
import {Label} from "@/components/ui/label";
import {Icon} from "@iconify/vue";
import axios from "axios";
import Toast from "@/components/ui/toast/Toast.vue";
import {useToast} from "@/composables/useToast";
import {router} from "@inertiajs/vue3";

const {showToast} = useToast()
const userAgreed = ref<boolean>(false);

const props = defineProps<{
  open: boolean;
  agreed: boolean;
}>();

const emit = defineEmits(['update:open'])

const isOpen = computed({
  get: () => props.open,
  set: (val) => emit('update:open', val)
})

const onUserAgreementCheck = () => {
  userAgreed.value = !userAgreed.value;
}

const onUserAgreementSubmit = async () => {
  let errorMsg = '';
  try {
    const response = await axios.post(route('my_properties.user_agreement'), []);
    if (response?.data && response?.data?.success) {
      isOpen.value = false
      router.reload();
      showToast('My Property User Agreement', response.data.success + ', Now can upload your properties', 'success');
      return true;
    }
    errorMsg = response?.data?.error ?? 'Something went wrong, Please try again!';
  } catch (error: any) {
    errorMsg = error?.toString() ?? 'Something went wrong, Please try again!';
  }
  showToast('My Property User Agreement', errorMsg, 'error');
}

</script>

<template>
  <Dialog v-if="isOpen" v-model:open="isOpen">
    <DialogContent class="h-[calc(100dvh-100px)]">
      <DialogHeader class="space-y-3">
        <DialogTitle class="text-center text-2xl">Terms and Conditions</DialogTitle>
        <VisuallyHidden>
          <DialogDescription>
            Terms and Conditions for Listing Properties on Revamp365.ai
          </DialogDescription>
        </VisuallyHidden>
      </DialogHeader>
      <div class="w-full h-full overflow-auto p-6 text-gray-900">
        <!-- Features and Benefits -->
        <section class="mb-6">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">Features and Benefits</h2>
          <ul class="list-disc space-y-2 pl-4 text-sm text-gray-700 dark:text-gray-200">
            <li>Get your deals in front of our quickly growing member base</li>
            <li>One centralized location to host your inventory</li>
            <li>Control over presented AVM, ARV, Rental estimates, rehab estimates, etc.</li>
            <li>Get your own "property store" with a custom URL you can send to your prospective buyers</li>
            <li>
              Ability to HIDE property address and only reveal it to verified buyers — This gives you the ability to vet
              and follow up with any prospective buyers
            </li>
            <li>User tracking — track views, clicks, shares, etc</li>
          </ul>
        </section>

        <!-- Terms and Conditions -->
        <section class="text-sm text-gray-600 dark:text-gray-300">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
            Terms and Conditions for Listing Properties on Revamp365.ai
          </h2>
          <div class="space-y-6">
            <!-- 1 -->
            <div>
              <h3 class="font-medium text-gray-700 dark:text-gray-200">1. Introduction</h3>
              <p class="mt-1">Welcome to Revamp365.ai. By listing a property on our platform, you agree to abide by
                these Terms and Conditions. Failure to comply may result in the removal of your listing and possible
                legal action.</p>
            </div>

            <!-- 2 -->
            <div>
              <h3 class="font-medium text-gray-700 dark:text-gray-200">2. Authority to List Property</h3>
              <ul class="list-disc list-inside ml-4 mt-1 space-y-1">
                <li>
                  As a user of Revamp365.ai, you assert that you have the legal authority to list the properties you
                  post. This authority must be evidenced by a written agreement with the property's rightful seller or
                  legal representative.
                </li>
                <li>
                  You are responsible for ensuring that all property listings on Revamp365.ai accurately reflect your
                  legal authority and any terms or conditions specified in your agreement with the property owner.
                </li>
              </ul>
            </div>

            <!-- 3 -->
            <div>
              <h3 class="font-medium text-gray-700 dark:text-gray-200">3. Responsibility and Liability</h3>
              <ul class="list-disc list-inside ml-4 mt-1 space-y-1">
                <li>
                  You, as the user, bear full responsibility for the authenticity and legality of the property listings
                  you post on Revamp365.ai. Our platform serves as a venue for listing properties and does not partake
                  in the actual transaction between buyers and sellers.
                </li>
                <li>
                  You agree to indemnify and hold harmless Revamp365.ai and its affiliates from any claims, losses,
                  damages, or liabilities arising from your property listings, including but not limited to
                  misrepresentation of your authority or disputes over property ownership.
                </li>
              </ul>
            </div>

            <!-- 4 -->
            <div>
              <h3 class="font-medium text-gray-700 dark:text-gray-200">4. Compliance with Laws</h3>
              <p class="mt-1">You are responsible for ensuring that your property listings comply with all applicable
                local, state, and federal laws and regulations.</p>
            </div>

            <!-- 5 -->
            <div>
              <h3 class="font-medium text-gray-700 dark:text-gray-200">5. Verification of Information</h3>
              <p class="mt-1">Revamp365.ai may, at its discretion, request additional documentation or information to
                verify your authority to list a property. Failure to provide such information may result in the removal
                of your listing from our platform.</p>
            </div>

            <!-- 6 -->
            <div>
              <h3 class="font-medium text-gray-700 dark:text-gray-200">6. Updating Your Listing</h3>
              <p class="mt-1">It is your responsibility to keep your listing up to date. If you accepted an offer, you
                should update your status promptly to pending, closed, etc. Keeping unavailable properties up is bad for
                you and your brand, bad for our platform, and wastes users' time. Failure to keep listings current can
                result not only in poor "seller scores" but could result in removal from the platform entirely.</p>
            </div>

            <!-- 7 -->
            <div>
              <h3 class="font-medium text-gray-700 dark:text-gray-200">7. Amendment of Terms</h3>
              <p class="mt-1">We reserve the right to amend these Terms and Conditions at any time. Your continued use
                of the platform after such amendments signifies your acceptance of the new terms.</p>
            </div>

            <!-- 8 -->
            <div>
              <h3 class="font-medium text-gray-700 dark:text-gray-200">8. Governing Law</h3>
              <p class="mt-1">These Terms and Conditions shall be governed and construed in accordance with the laws of
                the jurisdiction in which Revamp365.ai operates.</p>
            </div>

            <!-- 9 -->
            <div>
              <h3 class="font-medium text-gray-700 dark:text-gray-200">9. Acceptance of Terms</h3>
              <p class="mt-1">By listing a property on Revamp365.ai, you acknowledge that you have read, understood, and
                agree to be bound by these Terms and Conditions.</p>
            </div>
          </div>
          <div class="check mt-4" v-if="!props.agreed">
            <Label @click="onUserAgreementCheck()">
              <Icon v-if="userAgreed" class="size-6 text-primary" icon="tabler:square-check-filled"/>
              <Icon v-else class="size-6" icon="tabler:square-dashed"/>
              I have read and agree to the Terms and Conditions.
            </Label>
          </div>
        </section>
      </div>
      <DialogFooter class="gap-2">
        <DialogClose as-child>
          <Button variant="secondary"> Cancel</Button>
        </DialogClose>
        <Button v-if="!props.agreed" @click="onUserAgreementSubmit()" :disabled="!userAgreed" variant="default">Agree
        </Button>
        <Button v-else :disabled="true" variant="default">Already Agreed</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
  <Toast/>
</template>
<style scoped>

</style>
