/**
 * Formulário de prompt
 *
 * @package AICG
 */

import { useState, useCallback } from '@wordpress/element';
import {
    Button,
    TextareaControl,
    SelectControl,
    ToggleControl,
    Panel,
    PanelBody,
    PanelRow,
    __experimentalVStack as VStack,
    __experimentalHStack as HStack,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const PromptForm = ({ onSubmit, isLoading, settings }) => {
    const [prompt, setPrompt] = useState('');
    const [contentType, setContentType] = useState('post');
    const [tone, setTone] = useState(settings?.default_tone || 'professional');
    const [length, setLength] = useState(settings?.default_length || 'medium');
    const [includeImage, setIncludeImage] = useState(settings?.include_images !== false);
    const [scheduleDate, setScheduleDate] = useState(null);
    const [showAdvanced, setShowAdvanced] = useState(false);
    const [category, setCategory] = useState([]);

    const handleSubmit = useCallback(() => {
        if (!prompt.trim()) {
            return;
        }

        onSubmit({
            prompt: prompt.trim(),
            content_type: contentType,
            options: {
                tone,
                target_length: getLengthValue(length),
                include_images: includeImage,
                category,
            },
            schedule_date: scheduleDate,
        });
    }, [prompt, contentType, tone, length, includeImage, category, scheduleDate, onSubmit]);

    const getLengthValue = (len) => {
        const values = {
            short: 500,
            medium: 1000,
            long: 2000,
        };
        return values[len] || 1000;
    };

    const isValid = prompt.trim().length >= 10;

    return (
        <VStack spacing={4}>
            <TextareaControl
                label={__('Descreva o conteúdo que você quer gerar', 'ai-content-generator')}
                value={prompt}
                onChange={setPrompt}
                placeholder={__('Ex: Um artigo sobre benefícios da meditação para profissionais de TI...', 'ai-content-generator')}
                disabled={isLoading}
                rows={6}
                help={
                    prompt.length > 0 && prompt.length < 10
                        ? __('O prompt deve ter pelo menos 10 caracteres.', 'ai-content-generator')
                        : null
                }
            />

            <HStack alignment="stretch">
                <SelectControl
                    label={__('Tipo de Conteúdo', 'ai-content-generator')}
                    value={contentType}
                    options={[
                        { label: __('Post', 'ai-content-generator'), value: 'post' },
                        { label: __('Página', 'ai-content-generator'), value: 'page' },
                    ]}
                    onChange={setContentType}
                    disabled={isLoading}
                />

                <SelectControl
                    label={__('Tom de Voz', 'ai-content-generator')}
                    value={tone}
                    options={[
                        { label: __('Profissional', 'ai-content-generator'), value: 'professional' },
                        { label: __('Casual', 'ai-content-generator'), value: 'casual' },
                        { label: __('Técnico', 'ai-content-generator'), value: 'technical' },
                        { label: __('Persuasivo', 'ai-content-generator'), value: 'persuasive' },
                        { label: __('Narrativo', 'ai-content-generator'), value: 'narrative' },
                    ]}
                    onChange={setTone}
                    disabled={isLoading}
                />
            </HStack>

            <Panel>
                <PanelBody
                    title={__('Opções Avançadas', 'ai-content-generator')}
                    opened={showAdvanced}
                    onToggle={() => setShowAdvanced(!showAdvanced)}
                >
                    <PanelRow>
                        <SelectControl
                            label={__('Comprimento', 'ai-content-generator')}
                            value={length}
                            options={[
                                { label: __('Curto (300-500 palavras)', 'ai-content-generator'), value: 'short' },
                                { label: __('Médio (800-1200 palavras)', 'ai-content-generator'), value: 'medium' },
                                { label: __('Longo (1500-2500 palavras)', 'ai-content-generator'), value: 'long' },
                            ]}
                            onChange={setLength}
                            disabled={isLoading}
                        />
                    </PanelRow>

                    <PanelRow>
                        <ToggleControl
                            label={__('Incluir imagem destacada', 'ai-content-generator')}
                            checked={includeImage}
                            onChange={setIncludeImage}
                            disabled={isLoading}
                        />
                    </PanelRow>

                    <PanelRow>
                        <ToggleControl
                            label={__('Agendar publicação', 'ai-content-generator')}
                            checked={!!scheduleDate}
                            onChange={(checked) => setScheduleDate(checked ? new Date(Date.now() + 86400000).toISOString() : null)}
                            disabled={isLoading}
                        />
                    </PanelRow>
                </PanelBody>
            </Panel>

            <Button
                variant="primary"
                onClick={handleSubmit}
                disabled={!isValid || isLoading}
                isBusy={isLoading}
                className="aicg-generate-button"
            >
                {isLoading
                    ? __('Gerando...', 'ai-content-generator')
                    : __('Gerar Conteúdo', 'ai-content-generator')
                }
            </Button>
        </VStack>
    );
};

export default PromptForm;
