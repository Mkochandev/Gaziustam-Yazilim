<?php
return [
    'required'=>':attribute is required.', 'email'=>':attribute must be a valid email address.',
    'string'=>':attribute must be text.', 'array'=>':attribute contains invalid fields.',
    'in'=>'Choose a valid option for :attribute.', 'date_format'=>':attribute must match :format.',
    'accepted'=>'Please confirm :attribute.', 'boolean'=>':attribute must be yes or no.',
    'file'=>':attribute must be a file.', 'mimes'=>':attribute must be a file of type: :values.',
    'mimetypes'=>':attribute has an invalid file type.', 'extensions'=>':attribute has an invalid extension.',
    'max'=>['string'=>':attribute may have at most :max characters.','array'=>':attribute may have at most :max items.','file'=>':attribute may be at most :max KB.','numeric'=>':attribute may be at most :max.'],
    'min'=>['string'=>':attribute must have at least :min characters.','array'=>':attribute must have at least :min items.','file'=>':attribute must be at least :min KB.','numeric'=>':attribute must be at least :min.'],
    'integer'=>':attribute must be an integer.', 'uuid'=>':attribute is invalid.', 'prohibited'=>'Please leave :attribute empty.',
    'distinct'=>':attribute contains duplicate entries.',
    'attributes'=>['answers'=>'Answers','idempotency_key'=>'Reference','version_id'=>'Form version'],
];

