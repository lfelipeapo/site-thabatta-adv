/**
 * Indicador de status da geração
 *
 * @package AICG
 */

import {
    Spinner,
    __experimentalText as Text,
    __experimentalVStack as VStack,
    ProgressBar,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const StatusIndicator = ({ status }) => {
    if (status === 'idle') {
        return null;
    }

    const statusConfig = {
        validating: {
            icon: <Spinner />,
            message: __('Validando entrada...', 'ai-content-generator'),
            progress: 10,
        },
        queued: {
            icon: <span className="dashicons dashicons-clock" />,
            message: __('Na fila de processamento...', 'ai-content-generator'),
            progress: 20,
        },
        generating: {
            icon: <Spinner />,
            message: __('Gerando conteúdo com IA...', 'ai-content-generator'),
            progress: 50,
        },
        processing: {
            icon: <Spinner />,
            message: __('Processando resultado...', 'ai-content-generator'),
            progress: 80,
        },
        completed: {
            icon: <span className="dashicons dashicons-yes-alt" style={{ color: '#00a32a' }} />,
            message: __('Concluído!', 'ai-content-generator'),
            progress: 100,
        },
        failed: {
            icon: <span className="dashicons dashicons-dismiss" style={{ color: '#d63638' }} />,
            message: __('Falhou', 'ai-content-generator'),
            progress: 0,
        },
    };

    const config = statusConfig[status] || statusConfig.failed;

    return (
        <div className={`aicg-status-indicator aicg-status-${status}`}>
            <VStack spacing={3}>
                <div className="aicg-status-content">
                    {config.icon}
                    <Text>{config.message}</Text>
                </div>
                
                {status !== 'completed' && status !== 'failed' && (
                    <ProgressBar
                        value={config.progress}
                        className="aicg-status-progress"
                    />
                )}
            </VStack>
        </div>
    );
};

export default StatusIndicator;
