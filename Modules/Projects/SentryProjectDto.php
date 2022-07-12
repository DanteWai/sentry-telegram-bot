<?php

namespace Modules\Projects;

use System\DTO;

class SentryProjectDto extends DTO
{
    public string $id;
    public string $title;
    public string $slug;
}