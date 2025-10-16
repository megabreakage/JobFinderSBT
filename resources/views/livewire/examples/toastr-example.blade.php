<div class="max-w-4xl mx-auto p-6 space-y-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Toastr Notifications</h1>
        <p class="text-gray-600">Interactive toast notifications for user feedback</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Success Toast --}}
        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center gap-2">
                    <x-icon name="check-circle" size="md" class="text-green-600" />
                    <h3 class="text-lg font-medium">Success Toast</h3>
                </div>
            </x-slot:header>

            <p class="text-sm text-gray-600 mb-4">
                Display success messages for completed operations.
            </p>

            <x-ui.button variant="success" wire:click="showSuccess">
                Show Success
            </x-ui.button>
        </x-ui.card>

        {{-- Error Toast --}}
        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center gap-2">
                    <x-icon name="x-circle" size="md" class="text-red-600" />
                    <h3 class="text-lg font-medium">Error Toast</h3>
                </div>
            </x-slot:header>

            <p class="text-sm text-gray-600 mb-4">
                Display error messages when something goes wrong.
            </p>

            <x-ui.button variant="danger" wire:click="showError">
                Show Error
            </x-ui.button>
        </x-ui.card>

        {{-- Warning Toast --}}
        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center gap-2">
                    <x-icon name="exclamation-circle" size="md" class="text-yellow-600" />
                    <h3 class="text-lg font-medium">Warning Toast</h3>
                </div>
            </x-slot:header>

            <p class="text-sm text-gray-600 mb-4">
                Display warning messages to alert users.
            </p>

            <x-ui.button variant="warning" wire:click="showWarning">
                Show Warning
            </x-ui.button>
        </x-ui.card>

        {{-- Info Toast --}}
        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center gap-2">
                    <x-icon name="information-circle" size="md" class="text-blue-600" />
                    <h3 class="text-lg font-medium">Info Toast</h3>
                </div>
            </x-slot:header>

            <p class="text-sm text-gray-600 mb-4">
                Display informational messages to users.
            </p>

            <x-ui.button variant="primary" wire:click="showInfo">
                Show Info
            </x-ui.button>
        </x-ui.card>
    </div>

    {{-- Advanced Examples --}}
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-medium">Advanced Examples (PHPFlasher)</h3>
        </x-slot:header>

        <div class="space-y-4">
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Custom Toast</h4>
                <p class="text-sm text-gray-600 mb-3">
                    Toast with custom timeout and options
                </p>
                <x-ui.button variant="outline" wire:click="showCustom">
                    Show Custom Toast
                </x-ui.button>
            </div>

            <div class="border-t pt-4">
                <h4 class="font-medium text-gray-900 mb-2">Multiple Toasts</h4>
                <p class="text-sm text-gray-600 mb-3">
                    Display multiple notifications at once
                </p>
                <x-ui.button variant="outline" wire:click="showMultiple">
                    Show Multiple Toasts
                </x-ui.button>
            </div>

            <div class="border-t pt-4">
                <h4 class="font-medium text-gray-900 mb-2">Persistent Toast</h4>
                <p class="text-sm text-gray-600 mb-3">
                    Notification persists across Livewire updates
                </p>
                <x-ui.button variant="outline" wire:click="showPersistent">
                    Show Persistent Toast
                </x-ui.button>
            </div>

            <div class="border-t pt-4">
                <h4 class="font-medium text-gray-900 mb-2">Immediate Toast</h4>
                <p class="text-sm text-gray-600 mb-3">
                    Show notification immediately (current request only)
                </p>
                <x-ui.button variant="outline" wire:click="showImmediate">
                    Show Immediate Toast
                </x-ui.button>
            </div>

            <div class="border-t pt-4">
                <h4 class="font-medium text-gray-900 mb-2">Delayed Toast</h4>
                <p class="text-sm text-gray-600 mb-3">
                    Notification with custom delay before showing
                </p>
                <x-ui.button variant="outline" wire:click="showWithDelay">
                    Show Delayed Toast
                </x-ui.button>
            </div>

            <div class="border-t pt-4">
                <h4 class="font-medium text-gray-900 mb-2">Priority Toast</h4>
                <p class="text-sm text-gray-600 mb-3">
                    High priority notification
                </p>
                <x-ui.button variant="outline" wire:click="showWithPriority">
                    Show Priority Toast
                </x-ui.button>
            </div>
        </div>
    </x-ui.card>

    {{-- Code Examples --}}
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-medium">Usage Examples</h3>
        </x-slot:header>

        <div class="space-y-6">
            {{-- Livewire Component --}}
            <div>
                <h4 class="font-medium text-gray-900 mb-2">In Livewire Components</h4>
                <pre class="bg-gray-50 p-4 rounded-md overflow-x-auto text-xs"><code>use App\Traits\WithToastr;

class MyComponent extends Component
{
    use WithToastr;

    public function save()
    {
        // Your logic here
        
        $this->toastSuccess('Data saved successfully!');
        // or
        $this->toastError('Failed to save data.');
        // or
        $this->toastWarning('Please review your input.');
        // or
        $this->toastInfo('Processing your request...');
    }
}</code></pre>
            </div>

            {{-- Controller --}}
            <div>
                <h4 class="font-medium text-gray-900 mb-2">In Controllers</h4>
                <pre class="bg-gray-50 p-4 rounded-md overflow-x-auto text-xs"><code>use App\Traits\WithToastr;

class MyController extends Controller
{
    use WithToastr;

    public function store(Request $request)
    {
        // Your logic here
        
        $this->flashSuccess('Record created successfully!');
        return redirect()->back();
    }
}</code></pre>
            </div>

            {{-- Helper Functions --}}
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Using Helper Functions</h4>
                <pre class="bg-gray-50 p-4 rounded-md overflow-x-auto text-xs"><code>// Anywhere in your code
toast_success('Operation completed!');
toast_error('Something went wrong!');
toast_warning('Please be careful!');
toast_info('Here is some info!');

// Or with custom title
toast('success', 'Message here', 'Custom Title');</code></pre>
            </div>

            {{-- Blade Template --}}
            <div>
                <h4 class="font-medium text-gray-900 mb-2">In Blade Templates</h4>
                <pre class="bg-gray-50 p-4 rounded-md overflow-x-auto text-xs"><code>&lt;!-- Add to your layout file --&gt;
&lt;x-toastr /&gt;

&lt;!-- Or use JavaScript directly --&gt;
&lt;script&gt;
    toastr.success('Message', 'Title');
    toastr.error('Message', 'Title');
    toastr.warning('Message', 'Title');
    toastr.info('Message', 'Title');
&lt;/script&gt;</code></pre>
            </div>
        </div>
    </x-ui.card>

    {{-- Features --}}
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-medium">PHPFlasher Features</h3>
        </x-slot:header>

        <ul class="space-y-2 text-sm">
            <li class="flex items-start gap-2">
                <x-icon name="check" size="sm" class="text-green-600 mt-0.5" />
                <span><strong>Livewire Persistence:</strong> Notifications persist across Livewire updates</span>
            </li>
            <li class="flex items-start gap-2">
                <x-icon name="check" size="sm" class="text-green-600 mt-0.5" />
                <span><strong>Close Button:</strong> Users can dismiss notifications</span>
            </li>
            <li class="flex items-start gap-2">
                <x-icon name="check" size="sm" class="text-green-600 mt-0.5" />
                <span><strong>Progress Bar:</strong> Visual countdown before auto-dismiss</span>
            </li>
            <li class="flex items-start gap-2">
                <x-icon name="check" size="sm" class="text-green-600 mt-0.5" />
                <span><strong>Auto-dismiss:</strong> Notifications disappear after 5 seconds</span>
            </li>
            <li class="flex items-start gap-2">
                <x-icon name="check" size="sm" class="text-green-600 mt-0.5" />
                <span><strong>Stacking:</strong> Multiple notifications stack nicely</span>
            </li>
            <li class="flex items-start gap-2">
                <x-icon name="check" size="sm" class="text-green-600 mt-0.5" />
                <span><strong>Animations:</strong> Smooth fade in/out transitions</span>
            </li>
            <li class="flex items-start gap-2">
                <x-icon name="check" size="sm" class="text-green-600 mt-0.5" />
                <span><strong>Priority System:</strong> Control notification display order</span>
            </li>
            <li class="flex items-start gap-2">
                <x-icon name="check" size="sm" class="text-green-600 mt-0.5" />
                <span><strong>Delayed Display:</strong> Show notifications after a delay</span>
            </li>
            <li class="flex items-start gap-2">
                <x-icon name="check" size="sm" class="text-green-600 mt-0.5" />
                <span><strong>Session Flash:</strong> Supports Laravel session flash messages</span>
            </li>
            <li class="flex items-start gap-2">
                <x-icon name="check" size="sm" class="text-green-600 mt-0.5" />
                <span><strong>Validation Errors:</strong> Automatically displays validation errors</span>
            </li>
            <li class="flex items-start gap-2">
                <x-icon name="check" size="sm" class="text-green-600 mt-0.5" />
                <span><strong>Queue Support:</strong> Queue notifications for later display</span>
            </li>
        </ul>
    </x-ui.card>
</div>
