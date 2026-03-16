/**
 * Hook de geração de conteúdo
 *
 * @package AICG
 */

import { useState, useCallback, useRef, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * Hook para gerenciar o estado de geração
 */
export const useGeneration = () => {
    const [status, setStatus] = useState('idle');
    const [result, setResult] = useState(null);
    const [error, setError] = useState(null);
    const [jobId, setJobId] = useState(null);
    const pollIntervalRef = useRef(null);

    // Limpa polling ao desmontar
    useEffect(() => {
        return () => {
            if (pollIntervalRef.current) {
                clearInterval(pollIntervalRef.current);
            }
        };
    }, []);

    /**
     * Inicia geração de conteúdo
     */
    const generate = useCallback(async (formData) => {
        setStatus('validating');
        setError(null);
        setResult(null);
        setJobId(null);

        try {
            setStatus('generating');

            const response = await apiFetch({
                path: 'aicg/v1/generate',
                method: 'POST',
                data: formData,
            });

            if (!response.success) {
                throw new Error(response.message || 'Erro na geração');
            }

            // Se tem job_id, é assíncrono
            if (response.data.job_id) {
                setJobId(response.data.job_id);
                setStatus('queued');
                
                // Inicia polling
                startPolling(response.data.job_id);
                
                return { success: true, async: true, jobId: response.data.job_id };
            }

            // Síncrono - resultado imediato
            setStatus('completed');
            setResult(response);
            
            return { success: true, async: false, data: response.data };

        } catch (err) {
            setStatus('failed');
            setError(err.message || 'Erro desconhecido');
            return { success: false, error: err.message };
        }
    }, []);

    /**
     * Inicia polling de status
     */
    const startPolling = useCallback((id) => {
        if (pollIntervalRef.current) {
            clearInterval(pollIntervalRef.current);
        }

        const poll = async () => {
            try {
                const response = await apiFetch({
                    path: `aicg/v1/status/${id}`,
                });

                if (!response.success) {
                    throw new Error('Failed to fetch status');
                }

                const { data } = response;

                // Atualiza status
                setStatus(data.status);

                // Se completou ou falhou, para polling
                if (data.status === 'completed') {
                    setResult({ success: true, data: data.result });
                    clearInterval(pollIntervalRef.current);
                } else if (data.status === 'failed') {
                    setError(data.error || 'Geração falhou');
                    clearInterval(pollIntervalRef.current);
                }

            } catch (err) {
                console.error('Polling error:', err);
            }
        };

        // Poll imediatamente e depois a cada 3 segundos
        poll();
        pollIntervalRef.current = setInterval(poll, 3000);
    }, []);

    /**
     * Reseta estado
     */
    const reset = useCallback(() => {
        setStatus('idle');
        setResult(null);
        setError(null);
        setJobId(null);
        
        if (pollIntervalRef.current) {
            clearInterval(pollIntervalRef.current);
        }
    }, []);

    /**
     * Cancela job
     */
    const cancel = useCallback(async () => {
        if (!jobId) {
            return;
        }

        try {
            await apiFetch({
                path: `aicg/v1/cancel/${jobId}`,
                method: 'POST',
            });

            if (pollIntervalRef.current) {
                clearInterval(pollIntervalRef.current);
            }

            setStatus('cancelled');
        } catch (err) {
            console.error('Cancel error:', err);
        }
    }, [jobId]);

    return {
        status,
        result,
        error,
        jobId,
        generate,
        reset,
        cancel,
    };
};
