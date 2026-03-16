/**
 * Painel de preview do conteúdo gerado
 *
 * @package AICG
 */

import { useState } from '@wordpress/element';
import {
    Button,
    Panel,
    PanelBody,
    PanelRow,
    TabPanel,
    __experimentalHeading as Heading,
    __experimentalText as Text,
    __experimentalVStack as VStack,
    __experimentalHStack as HStack,
    Modal,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const PreviewPanel = ({ result, onReset, onEdit }) => {
    const { data } = result;
    const [showSeo, setShowSeo] = useState(false);

    if (!data) {
        return null;
    }

    const { post_id, status, edit_link, preview_link, generation_metadata } = data;

    return (
        <div className="aicg-preview">
            <HStack className="aicg-preview-header" justify="space-between">
                <Heading level={2}>
                    {__('Conteúdo Gerado!', 'ai-content-generator')}
                </Heading>
                <HStack>
                    <Button
                        variant="secondary"
                        href={edit_link}
                        target="_blank"
                    >
                        {__('Editar no WordPress', 'ai-content-generator')}
                    </Button>
                    <Button
                        variant="primary"
                        onClick={onReset}
                    >
                        {__('Gerar Novo', 'ai-content-generator')}
                    </Button>
                </HStack>
            </HStack>

            <Panel className="aicg-preview-panel">
                <PanelBody title={__('Detalhes', 'ai-content-generator')} initialOpen={true}>
                    <PanelRow>
                        <Text>
                            <strong>{__('Status:', 'ai-content-generator')}</strong>{' '}
                            {status === 'draft'
                                ? __('Rascunho', 'ai-content-generator')
                                : __('Agendado', 'ai-content-generator')
                            }
                        </Text>
                    </PanelRow>
                    <PanelRow>
                        <Text>
                            <strong>{__('Modelo:', 'ai-content-generator')}</strong>{' '}
                            {generation_metadata?.model_used}
                        </Text>
                    </PanelRow>
                    <PanelRow>
                        <Text>
                            <strong>{__('Tokens:', 'ai-content-generator')}</strong>{' '}
                            {generation_metadata?.tokens_total || 
                             (generation_metadata?.tokens_input + generation_metadata?.tokens_output)}
                        </Text>
                    </PanelRow>
                </PanelBody>
            </Panel>

            <div className="aicg-preview-content">
                <TabPanel
                    className="aicg-preview-tabs"
                    activeClass="active-tab"
                    tabs={[
                        {
                            name: 'preview',
                            title: __('Visualização', 'ai-content-generator'),
                        },
                        {
                            name: 'seo',
                            title: __('SEO', 'ai-content-generator'),
                        },
                    ]}
                >
                    {(tab) => (
                        <div className="aicg-preview-tab-content">
                            {tab.name === 'preview' && (
                                <ContentPreview result={result} />
                            )}
                            {tab.name === 'seo' && (
                                <SeoPreview result={result} />
                            )}
                        </div>
                    )}
                </TabPanel>
            </div>
        </div>
    );
};

/**
 * Preview do conteúdo
 */
const ContentPreview = ({ result }) => {
    // O resultado pode vir de geração síncrona ou assíncrona
    // Na síncrona temos post_id direto, na assíncrona precisamos buscar
    
    return (
        <VStack spacing={4}>
            <div className="aicg-content-preview">
                <p className="aicg-preview-note">
                    {__('O conteúdo foi salvo como rascunho. Clique em "Editar no WordPress" para visualizar e editar.', 'ai-content-generator')}
                </p>
                
                <div className="aicg-preview-actions">
                    <Button
                        variant="secondary"
                        href={result.data?.preview_link}
                        target="_blank"
                    >
                        {__('Ver Preview', 'ai-content-generator')}
                    </Button>
                    
                    <Button
                        variant="tertiary"
                        onClick={() => {
                            window.open(result.data?.edit_link, '_blank');
                        }}
                    >
                        {__('Editar Conteúdo', 'ai-content-generator')}
                    </Button>
                </div>
            </div>
        </VStack>
    );
};

/**
 * Preview de SEO
 */
const SeoPreview = ({ result }) => {
    // SEO info would be in the generation result
    // This is a simplified version
    
    return (
        <div className="aicg-seo-preview">
            <VStack spacing={3}>
                <div className="aicg-seo-field">
                    <Heading level={4}>
                        {__('Título SEO', 'ai-content-generator')}
                    </Heading>
                    <Text>
                        {__('Disponível na página de edição do post.', 'ai-content-generator')}
                    </Text>
                </div>
                
                <div className="aicg-seo-field">
                    <Heading level={4}>
                        {__('Meta Description', 'ai-content-generator')}
                    </Heading>
                    <Text>
                        {__('Disponível na página de edição do post.', 'ai-content-generator')}
                    </Text>
                </div>
                
                <div className="aicg-seo-field">
                    <Heading level={4}>
                        {__('Palavra-chave Principal', 'ai-content-generator')}
                    </Heading>
                    <Text>
                        {__('Disponível na página de edição do post.', 'ai-content-generator')}
                    </Text>
                </div>
            </VStack>
        </div>
    );
};

export default PreviewPanel;
