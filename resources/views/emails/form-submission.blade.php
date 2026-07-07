<x-mail::message>
# {{ __('New form submission') }}

**{{ __('Form') }}:** {{ $form->name }}

@if ($data->email)
**{{ __('Email') }}:** {{ $data->email }}
@endif
@if ($data->name)
**{{ __('Name') }}:** {{ $data->name }}
@endif
@if ($data->phone)
**{{ __('Phone') }}:** {{ $data->phone }}
@endif

@if ($data->notes !== '')
---
{!! nl2br(e($data->notes)) !!}
@endif

@if ($customer)
<x-mail::button :url="url('/')">
{{ __('Open in CRM') }}
</x-mail::button>
@endif

{{ config('app.name') }}
</x-mail::message>
