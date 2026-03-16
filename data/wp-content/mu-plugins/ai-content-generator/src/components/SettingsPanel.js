/**
 * Painel de configurações
 *
 * @package AICG
 */

import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import {
    Button,
    TextControl,
    SelectControl,
    ToggleControl,
    Notice,
    __experimentalVStack as VStack,
    __experimentalHeading as Heading,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const SettingsPanel = ({ settings, onSave }) => {
    const [formData, setFormData] = useState({
        api_key: '',
        default_model: settings?.default_model || 'llama-3.3-70b-versatile',
        default_tone: settings?.default_tone || 'professional',
        default_length: settings?.default_length || 'medium',
        include_images: settings?.include_images !== false,
        cache_enabled: settings?.cache_enabled !== false,
        async_generation: settings?.async_generation !== false,
    });
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState(null);
    const [success, setSuccess] = useState(false);

    const handleSave = async () => {
        setSaving(true);
        setError(null);
        setSuccess(false);

        try {
            const response = await apiFetch({
                path: 'aicg/v1/settings',
                method: 'POST',
                data: formData,
            });

            if (response.success) {
                setSuccess(true);
                onSave?.(formData);
            } else {
                setError(response.message || __('Erro ao salvar.', 'ai-content-generator'));
            }
        } catch (err) {
            setError(err.message || __('Erro ao salvar.', 'ai-content-generator'));
        } finally {
            setSaving(false);
        }
    };

    const handleValidateApi = async () => {
        if (!formData.api_key) {
            return;
        }

        setSaving(true);
        setError(null);

        try {
            const response = await apiFetch({
                path: 'aicg/v1/validate-api',
                method: 'POST',
                data: { api_key: formData.api_key },
            });

            if (response.success) {
                setSuccess(true);
            } else {
                setError(response.message || __('Chave API inválida.', 'ai-content-generator'));
            }
        } catch (err) {
            setError(err.message || __('Erro ao validar chave.', 'ai-content-generator'));
        } finally {
            setSaving(false);
        }
    };

    return (
        <VStack spacing={4} className="aicg-settings-panel">
            {error && (
                <Notice status="error" isDismissible onDismiss={() => setError(null)}>
                    {error}
                </Notice>
            )}

            {success && (
                <Notice status="success" isDismissible onDismiss={() => setSuccess(false)}>
                    {__('Configurações salvas!', 'ai-content-generator')}
                </Notice>
            )}

            <VStack spacing={3}>
                <Heading level={3}>
                    {__('API Groq', 'ai-content-generator')}
                </Heading>

                <TextControl
                    label={__('Chave API', 'ai-content-generator')}
                    type="password"
                    value={formData.api_key}
                    onChange={(api_key) => setFormData({ ...formData, api_key })}
                    placeholder={settings?.api_key_configured ? __('••••••••••••••••', 'ai-content-generator') : ''}
                    help={settings?.api_key_configured 
                        ? __('Chave API já configurada. Deixe em branco para manter.', 'ai-content-generator')
                        : __('Obtenha sua chave em console.groq.com', 'ai-content-generator')
                    }
                />

                {formData.api_key && (
                    <Button
                        variant="secondary"
                        onClick={handleValidateApi}
                        isBusy={saving}
                        disabled={!formData.api_key}
                    >
                        {__('Validar Chave', 'ai-content-generator')}
                    </Button>
                )}

                <SelectControl
                    label={__('Modelo Padrão', 'ai-content-generator')}
                    value={formData.default_model}
                    options={[
                        { label: 'Llama 3.3 70B', value: 'llama-3.3-70b-versatile' },
                        { label: 'Mixtral 8x7B', value: 'mixtral-8x7b-32768' },
                        { label: 'Gemma 7B', value: 'gemma-7b-it' },
                    ]}
                    onChange={(default_model) => setFormData({ ...formData, default_model })}
                />
            </VStack>

            <VStack spacing={3}>
                <Heading level={3}>
                    {__('Preferências', 'ai-content-generator')}
                </Heading>

                <SelectControl
                    label={__('Tom de Voz Padrão', 'ai-content-generator')}
                    value={formData.default_tone}
                    options={[
                        { label: __('Profissional', 'ai-content-generator'), value: 'professional' },
                        { label: __('Casual', 'ai-content-generator'), value: 'casual' },
                        { label: __('Técnico', 'ai-content-generator'), value: 'technical' },
                        { label: __('Persuasivo', 'ai-content-generator'), value: 'persuasive' },
                        { label: __('Narrativo', 'ai-content-generator'), value: 'narrative' },
                    ]}
                    onChange={(default_tone) => setFormData({ ...formData, default_tone })}
                />

                <SelectControl
                    label={__('Comprimento Padrão', 'ai-content-generator')}
                    value={formData.default_length}
                    options={[
                        { label: __('Curto (300-500 palavras)', 'ai-content-generator'), value: 'short' },
                        { label: __('Médio (800-1200 palavras)', 'ai-content-generator'), value: 'medium' },
                        { label: __('Longo (1500-2500 palavras)', 'ai-content-generator'), value: 'long' },
                    ]}
                    onChange={(default_length) => setFormData({ ...formData, default_length })}
                />

                <ToggleControl
                    label={__('Incluir imagens destacadas', 'ai-content-generator')}
                    checked={formData.include_images}
                    onChange={(include_images) => setFormData({ ...formData, include_images })}
                />

                <ToggleControl
                    label={__('Habilitar cache de respostas', 'ai-content-generator')}
                    checked={formData.cache_enabled}
                    onChange={(cache_enabled) => setFormData({ ...formData, cache_enabled })}
                />

                <ToggleControl
                    label={__('Usar processamento assíncrono', 'ai-content-generator')}
                    checked={formData.async_generation}
                    onChange={(async_generation) => setFormData({ ...formData, async_generation })}
                    help={__('Recomendado para evitar timeouts em gerações longas.', 'ai-content-generator')}
                />
            </VStack>

            <Button
                variant="primary"
                onClick={handleSave}
                isBusy={saving}
                disabled={saving}
            >
                {saving 
                    ? __('Salvando...', 'ai-content-generator')
                    : __('Salvar Configurações', 'ai-content-generator')
                }
            </Button>
        </VStack>
    );
};

export default SettingsPanel;
