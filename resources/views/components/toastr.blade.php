{{-- Simple Toast Notifications Component --}}
{{-- This component renders session flash messages as toast notifications --}}

<div x-data="toastManager()" 
     @flash-message.window="addToast($event.detail)"
     class="fixed top-4 right-4 z-50 space-y-2">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full"
             :class="{
                 'bg-green-50 border-green-200 text-green-800': toast.type === 'success',
                 'bg-red-50 border-red-200 text-red-800': toast.type === 'error',
                 'bg-yellow-50 border-yellow-200 text-yellow-800': toast.type === 'warning',
                 'bg-blue-50 border-blue-200 text-blue-800': toast.type === 'info'
             }"
             class="flex items-center p-4 rounded-lg border shadow-lg max-w-sm">
            <div class="flex-shrink-0">
                <svg x-show="toast.type === 'success'" class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                <svg x-show="toast.type === 'error'" class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                </svg>
                <svg x-show="toast.type === 'warning'" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                <svg x-show="toast.type === 'info'" class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium" x-text="toast.message"></p>
            </div>
            <button @click="removeToast(toast.id)" class="ml-4 flex-shrink-0 inline-flex text-gray-400 hover:text-gray-500">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </template>
</div>

{{-- Session flash messages --}}
@if(session()->has('success'))
    <script>
        window.dispatchEvent(new CustomEvent('flash-message', {
            detail: { type: 'success', message: @js(session('success')) }
        }));
    </script>
@endif

@if(session()->has('error'))
    <script>
        window.dispatchEvent(new CustomEvent('flash-message', {
            detail: { type: 'error', message: @js(session('error')) }
        }));
    </script>
@endif

@if(session()->has('warning'))
    <script>
        window.dispatchEvent(new CustomEvent('flash-message', {
            detail: { type: 'warning', message: @js(session('warning')) }
        }));
    </script>
@endif

@if(session()->has('info'))
    <script>
        window.dispatchEvent(new CustomEvent('flash-message', {
            detail: { type: 'info', message: @js(session('info')) }
        }));
    </script>
@endif

<script>
    function toastManager() {
        return {
            toasts: [],
            nextId: 1,
            
            addToast(data) {
                const id = this.nextId++;
                const toast = {
                    id: id,
                    type: data.type || 'info',
                    message: data.message,
                    visible: true
                };
                
                this.toasts.push(toast);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    this.removeToast(id);
                }, 5000);
            },
            
            removeToast(id) {
                const index = this.toasts.findIndex(t => t.id === id);
                if (index > -1) {
                    this.toasts[index].visible = false;
                    setTimeout(() => {
                        this.toasts.splice(index, 1);
                    }, 300);
                }
            }
        }
    }
</script>
