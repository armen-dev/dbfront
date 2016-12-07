<?php

// Echo
stream_copy_to_stream(fopen('php://input', 'r'), fopen('php://output', 'w'));
