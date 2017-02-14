angular.module('periodic.config', [])
	.constant("messages",
{
     200: 'String is too short! ({{viewValue.length}} chars), minimum {{schema.minLength}}',
     201: 'String is too long! ({{viewValue.length}} chars), maximum {{schema.maxLength}}'
});